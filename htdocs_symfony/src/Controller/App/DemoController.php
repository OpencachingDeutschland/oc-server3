<?php

namespace Oc\Controller\App;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DemoController extends AbstractController
{
    public function __construct(
        private Connection $connection,
        private Security $security
    ) {}

    #[Route('/demo/tabulator', name: 'demo_tabulator')]
    public function tabulator(): Response
    {
        return $this->render('demo/tabulator.html.twig');
    }

    #[Route('/demo/ag-grid', name: 'demo_ag-grid')]
    public function agGrid(): Response
    {
        return $this->render('demo/ag-grid.html.twig');
    }

    #[Route('/demo/ssr', name: 'demo_ssr')]
    public function ssr(): Response
    {
        return $this->render('demo/ssr.html.twig', [
            'items' => [
                ['id' => 1, 'name' => 'Item 1', 'value' => 'Value 1'],
                ['id' => 2, 'name' => 'Item 2', 'value' => 'Value 2'],
                ['id' => 3, 'name' => 'Item 3', 'value' => 'Value 3'],
            ]
        ]);
    }


    #[Route('/map3-api', name: 'demo_map3_api')]
    public function map3Api(Request $request): JsonResponse
    {
        $mode = $request->query->get('mode');

        if ($mode === "live") {
            return $this->outputLiveMarkers($request);
        }

        return new JsonResponse(['error' => 'invalid mode'], 400);
    }

    private function outputLiveMarkers(Request $request): JsonResponse
    {
        $lat1 = (float)$request->query->get('lat1', 0);
        $lat2 = (float)$request->query->get('lat2', 0);
        $lon1 = (float)$request->query->get('lon1', 0);
        $lon2 = (float)$request->query->get('lon2', 0);

        if ($lat1 >= $lat2 || $lon1 >= $lon2) {
            return new JsonResponse(['count' => 0, 'items' => []]);
        }

        $maxItems = 5000;
        $userId = $this->security->getUser()?->getId() ?? 0;
        $isAdmin = $this->security->isGranted('ROLE_ADMIN'); // Define isAdmin check


        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder
            ->select('COUNT(*)')
            ->from('caches')
            ->where('c.latitude > :lat1 AND c.latitude < :lat2')
            ->andWhere('c.longitude > :lon1 AND c.longitude < :lon2')
            ->andWhere($queryBuilder->expr()->orX(
                $queryBuilder->expr()->eq('cs.allowUserView', ':allowUserView'),
                $queryBuilder->expr()->eq('c.user_id', ':userId'),
                ($isAdmin ? $queryBuilder->expr()->neq('c.status', ':statusArchived') : ':statusArchived') // Include if admin and not archived
            ))
            ->groupBy('c.cache_id')
            ->orderBy('c.cache_id')
            ->setMaxResults($maxItems)
            ->setParameters([
                'lat1' => $lat1,
                'lat2' => $lat2,
                'lon1' => $lon1,
                'lon2' => $lon2,
                'userId' => $userId,
                'allowUserView' => 1,
                'statusArchived' => 5, // Assuming status 5 is archived
            ]);

        $count = (int)$this->connection->fetchOne($queryBuilder->getSQL(), $queryBuilder->getParameters());

        if ($count > $maxItems) {
            return new JsonResponse(['count' => $count, 'items' => []]);
        }

        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder
            ->select(
                'c.cache_id',
                'c.wp_oc AS referenceCode',
                'c.name',
                'c.latitude AS listingLat',
                'c.longitude AS listingLon',
                'c.type AS typeId',
                'ct.en AS typeName',
                'c.size AS sizeId',
                'cs.name AS sizeName',
                'c.difficulty / 2 AS difficulty',
                'c.terrain / 2 AS terrain',
                'c.status',
                'u.username AS ownerAlias',
                'c.user_id AS ownerCode',
                'c.date_created AS publishedDate',
                'IFNULL(sc.toprating, 0) AS favoritePoints',
                'IFNULL(sc.found, 0) AS findCount',
                'IF(c.user_id = :userId, 1, 0) AS isOwned',
                'IF(fl.id IS NOT NULL, 1, 0) AS isFound',
                'MAX(fl.date) AS foundDate',
                'IF(pcn.id IS NOT NULL, 1, 0) AS hasPCN',
                'IF(pcn.id IS NOT NULL AND pcn.latitude != 0 AND pcn.longitude != 0, 1, 0) AS hasCC',
                'pcn.latitude AS ccLat',
                'pcn.longitude AS ccLon',
                'pcn.description AS pcnText'
            )
            ->from('caches', 'c')
            ->innerJoin('c', 'cache_type', 'ct', 'c.type = ct.id')
            ->innerJoin('c', 'cache_size', 'cs', 'c.size = cs.id')
            ->innerJoin('c', 'user', 'u', 'c.user_id = u.user_id')
            ->leftJoin('c', 'stat_caches', 'sc', 'c.cache_id = sc.cache_id')
            ->leftJoin('c', 'cache_logs', 'fl', 'fl.cache_id = c.cache_id AND fl.user_id = :userId AND fl.type IN (1, 7)')
            ->leftJoin('c', 'coordinates', 'pcn', 'pcn.cache_id = c.cache_id AND pcn.user_id = :userId AND pcn.type = 2')
            ->where('c.latitude > :lat1 AND c.latitude < :lat2')
            ->andWhere('c.longitude > :lon1 AND c.longitude < :lon2')
            ->andWhere('c.status IN (1, 2)')
            ->groupBy('c.cache_id')
            ->orderBy('c.cache_id')
            ->setMaxResults($maxItems)
            ->setParameters([
                'lat1' => $lat1,
                'lat2' => $lat2,
                'lon1' => $lon1,
                'lon2' => $lon2,
                'userId' => $userId,
            ]);

        $rows = $this->connection->fetchAllAssociative($queryBuilder->getSQL(), $queryBuilder->getParameters());

        $items = [];
        foreach ($rows as $r) {
            $isDisabled = ((int)$r['status'] === 2);
            $hasCC = (bool)(int)$r['hasCC'];
            $lat = $hasCC ? (float)$r['ccLat'] : (float)$r['listingLat'];
            $lon = $hasCC ? (float)$r['ccLon'] : (float)$r['listingLon'];

            $items[] = [
                'referenceCode' => $r['referenceCode'],
                'name' => $r['name'],
                'lat' => $lat,
                'lon' => $lon,
                'listingLat' => (float)$r['listingLat'],
                'listingLon' => (float)$r['listingLon'],
                'geocacheType' => [
                    'id' => (int)$r['typeId'],
                    'name' => $r['typeName'],
                ],
                'geocacheSize' => [
                    'id' => (int)$r['sizeId'],
                    'name' => $r['sizeName'],
                ],
                'difficulty' => (float)$r['difficulty'],
                'terrain' => (float)$r['terrain'],
                'isDisabled' => ((int)$r['allowUserView'] === 0 || (int)$r['status'] === 2 && (int)$r['isOwned'] === 0 && (int)$r['isFound'] === 0), // Adjusted logic for isDisabled
                'isFound' => (bool)(int)$r['isFound'],
                'foundDate' => $r['foundDate'] ? date('Y-m-d', strtotime($r['foundDate'])) : '',
                'isOwned' => (bool)(int)$r['isOwned'],
                'ownerAlias' => $r['ownerAlias'],
                'ownerCode' => (string)$r['ownerCode'],
                'publishedDate' => date('Y-m-d', strtotime($r['publishedDate'])),
                'favoritePoints' => (int)$r['favoritePoints'],
                'findCount' => (int)$r['findCount'],
            ];
            }

            return new JsonResponse(['count' => $count, 'items' => $items]);
            }
            }
