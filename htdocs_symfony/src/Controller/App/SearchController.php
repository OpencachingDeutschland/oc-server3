<?php

declare(strict_types=1);

namespace Oc\Controller\App;

use Oc\Repository\CachesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Oc\Security\Auth;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class SearchController extends AbstractController
{
    public function __construct(
        private CachesRepository $cachesRepository,
        private Auth $auth
    ) {}

    #[Route('/api/caches/live', name: 'api_caches_live')]
    public function liveCaches(Request $request): JsonResponse
    {
        $lat1 = (float)$request->query->get('lat1', 0);
        $lat2 = (float)$request->query->get('lat2', 0);
        $lon1 = (float)$request->query->get('lon1', 0);
        $lon2 = (float)$request->query->get('lon2', 0);
        $minDiff = (int)$request->query->get('minDiff', 2);
        $maxDiff = (int)$request->query->get('maxDiff', 10);

        if ($lat1 >= $lat2 || $lon1 >= $lon2) {
            return new JsonResponse(['count' => 0, 'items' => []]);
        }

        $maxItems = 5000;
        $userId = (int) ($this->auth->getUser()['user_id'] ?? 0);

        $count = $this->cachesRepository->countCachesInBounds(
            $lat1, $lat2, $lon1, $lon2, $minDiff, $maxDiff
        );

        if ($count > $maxItems) {
            return new JsonResponse(['count' => $count, 'items' => []]);
        }

        $rs = $this->cachesRepository->fetchCachesInBounds(
            $lat1, $lat2, $lon1, $lon2, $minDiff, $maxDiff, $userId, $maxItems
        );

        $items = [];
        foreach ($rs as $r) {
            $hasCC = (bool)(int)$r['hasCC'];
            $lat   = $hasCC ? (float)$r['ccLat'] : (float)$r['listingLat'];
            $lon   = $hasCC ? (float)$r['ccLon'] : (float)$r['listingLon'];

            $items[] = [
                '_id'            => $r['referenceCode'],
                'referenceCode'  => $r['referenceCode'],
                'platform'       => 'OC',
                'name'           => $r['name'],
                'lat'            => $lat,
                'lon'            => $lon,
                'listingLat'     => (float)$r['listingLat'],
                'listingLon'     => (float)$r['listingLon'],
                'geocacheType'   => ['id' => (int)$r['typeId'], 'name' => $r['typeName']],
                'geocacheSize'   => ['id' => (int)$r['sizeId'], 'name' => $r['sizeName']],
                'difficulty'     => (float)$r['difficulty'],
                'terrain'        => (float)$r['terrain'],
                'isArchived'     => false,
                'isDisabled'     => ((int)$r['status'] === 2),
                'isFound'        => (bool)(int)$r['isFound'],
                'foundDate'      => $r['foundDate'] ? (new \DateTime($r['foundDate']))->format('Y-m-d') : '',
                'isOwned'        => (bool)(int)$r['isOwned'],
                'isSelected'     => false,
                'ownerAlias'     => $r['ownerAlias'],
                'ownerCode'      => (string)$r['ownerCode'],
                'publishedDate'  => (new \DateTime($r['publishedDate']))->format('Y-m-d'),
                'favoritePoints' => (int)$r['favoritePoints'],
                'findCount'      => (int)$r['findCount'],
                'shortName'      => mb_strlen($r['name']) > 25 ? mb_substr($r['name'], 0, 25) . '…' : $r['name'],
                'hasPCN'         => (bool)(int)$r['hasPCN'],
                'hasCC'          => (bool)(int)$r['hasCC'],
                'pcn'            => $r['pcnText'] ?? '',
                'isOcOnly'       => (bool)(int)$r['isOcOnly'],
            ];
        }

        return new JsonResponse(['count' => $count, 'items' => $items]);
    }
}
