<?php

declare(strict_types=1);

namespace Oc\Controller\App;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Exception;
use Oc\Form\CachesFormType;
use Oc\Repository\CachesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CachesController extends AbstractController
{

    private Connection $connection;

    private CachesRepository $cachesRepository;

    public function __construct(Connection $connection, CachesRepository $cachesRepository)
    {
        $this->connection = $connection;
        $this->cachesRepository = $cachesRepository;
    }

    /**
     * @throws Exception
     * @Route("/caches", name="caches_index")
     */
    public function cachesController_index(Request $request): Response
    {
        $fetchedCaches = '';

        // create input field for caches_by_searchfield
        $form = $this->createForm(CachesFormType::class);

        // see: https://symfonycasts.com/screencast/symfony-forms/form-submit
        // handles the request (submit-button of the form), but only if there is a POST request
        $form->handleRequest($request);
        // is true only if there is a request submitted, and it is valid
        if ($form->isSubmitted() && $form->isValid()) {
            // read content of form input field
            $inputData = $form->getData();

            // send request to DB
            $fetchedCaches = $this->getCachesForSearchField($inputData['content_searchfield']);

            // extra search round in case someone is too lazy and just inputs OC/GC waypoint without prefixed 'OC' or 'GC' (example: 100f instead of OC100F)
            if (empty($fetchedCaches)) {
                if (preg_match('/[a-zA-Z0-9]{3,5}/', $inputData['content_searchfield'])
                    && !str_starts_with($inputData['content_searchfield'], 'OC')
                    && !str_starts_with($inputData['content_searchfield'], 'GC')
                ) {
                    $fetchedCaches = $this->getCachesForSearchFieldWPOnly($inputData['content_searchfield']);
                }
            }
        }

        return $this->render(
                'app/caches/search.html.twig', [
                        'cachesForm' => $form->createView(),
                        'caches_by_searchfield' => $fetchedCaches
                ]
        );
    }

    /**
     * @Route("/cache/{wpID}", name="cache_by_wp_oc_gc")
     */
    public function search_by_cache_wp(string $wpID): Response
    {
        try {
            $fetchedCache = $this->cachesRepository->fetchOneByOCWaypoint($wpID);
        } catch (Exception $e) {
            //  tue was..
        }

        return $this->render('app/caches/view_listing.html.twig', ['cache' => $fetchedCache]
        );
    }


    /**
     * @param string $searchtext
     *
     * @return array
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function getCachesForSearchField(string $searchtext)
    : array {
        //      so sieht die SQL-Vorlage aus..
        //        SELECT cache_id, name, wp_oc, user.username
        //        FROM caches
        //        INNER JOIN user ON caches.user_id = user.user_id
        //        WHERE wp_oc         =       "' . $searchtext . '"
        //        OR wp_gc            =       "' . $searchtext . '"
        //        OR caches.name     LIKE    "%' . $searchtext . '%"'
        //        OR user.username   LIKE    "%' . $searchtext . '%"'
        $qb = $this->connection->createQueryBuilder();
        $qb->select('caches.cache_id', 'caches.name', 'caches.wp_oc', 'caches.wp_gc', 'user.username')
            ->from('caches')
            ->innerJoin('caches', 'user', 'user', 'caches.user_id = user.user_id')
            ->where('caches.wp_oc = :searchTerm')
            ->orWhere('caches.wp_gc = :searchTerm')
            ->orWhere('caches.name LIKE :searchTermLIKE')
            ->orWhere('user.username LIKE :searchTermLIKE')
            ->setParameters(['searchTerm' => $searchtext, 'searchTermLIKE' => '%' . $searchtext . '%'])
            ->orderBy('caches.wp_oc', 'ASC');

        return $qb->execute()->fetchAllAssociative();
    }

    /**
     * @param int $id
     *
     * @return array
     * @throws RecordNotFoundException
     */
    public function getCacheDetailsById(int $id)
    : array {
        $fetchedCache = $this->cachesRepository->fetchOneBy(['cache_id' => $id]);

        return [$this->cachesRepository->getDatabaseArrayFromEntity($fetchedCache)];
    }

    /**
     * @param string $wayPoint
     *
     * @return array
     * @throws RecordNotFoundException
     */
    public function getCacheDetailsByWayPoint(string $wayPoint)
    : array {
        $fetchedCache = $this->cachesRepository->fetchOneBy(['wp_oc' => $wayPoint]);

        return [$this->cachesRepository->getDatabaseArrayFromEntity($fetchedCache)];
    }
}
