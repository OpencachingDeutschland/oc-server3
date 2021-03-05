<?php

declare(strict_types=1);

namespace Oc\Controller\Backend;

use Doctrine\DBAL\Connection;
use Oc\Form\CachesFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\CachesRepository;

/**
 * Class CachesController
 *
 * @package Oc\Controller\Backend
 */
class CachesController extends AbstractController
{
    private $connection;

    private $cachesRepository;

    /**
     * CachesController constructor.
     *
     * @param Connection $connection
     * @param CachesRepository $cachesRepository
     */
    public function __construct(Connection $connection, CachesRepository $cachesRepository)
    {
        $this->connection = $connection;
        $this->cachesRepository = $cachesRepository;
    }

    /**
     * @param Request $request
     * @Route("/caches", name="caches_index")
     *
     * @return Response
     */
    public function cachesController_index(Request $request)
    : Response {
        $fetchedCaches = '';

        // create input field for caches_by_searchfield
        $form = $this->createForm(CachesFormType::class);

        // see: https://symfonycasts.com/screencast/symfony-forms/form-submit
        // handles the request (submit-button of the form), but only if there is a POST request
        $form->handleRequest($request);
        // if is true only if there is a request submitted and it is valid
        if ($form->isSubmitted() && $form->isValid()) {
            // read content of form input field
            $inputData = $form->getData();

            // send request to DB
            $fetchedCaches = $this->getCachesForSearchField($inputData['content_caches_searchfield']);
        }

        if ($fetchedCaches === '') {
            return $this->render(
                'backend/caches/index.html.twig', [
                                                    'cachesForm' => $form->createView(),
                                                ]
            );
        } else {
            return $this->render(
                'backend/caches/basicview.html.twig', [
                                                        'cachesForm' => $form->createView(),
                                                        'caches_by_searchfield' => $fetchedCaches
                                                    ]
            );
        }
    }

    /**
     * @param string $wpID
     *
     * @return Response
     * @Route("/cache/{wpID}", name="cache_by_wp_oc_gc")
     */
    public function search_by_cache_wp(string $wpID)
    : Response {
        $fetchedCaches = [];

        try {
            $fetchedCaches = $this->getCacheDetailsByWayPoint($wpID);
        } catch (\Exception $e) {
            //  tue was.. (status_not_found = true);
        }

        return $this->render('backend/caches/detailview.html.twig', ['cache_by_id' => $fetchedCaches]); //+ status_not_found + abfragen in twig, Z.B.
    }

    /**
     * @param string $searchtext
     *
     * @return array
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

        return $qb->execute()->fetchAll();
    }

    /**
     * @param int $id
     *
     * @return array
     * @throws RecordNotFoundException
     */
    public function getCacheDetailsById(int $id) : array {
        $fetchedCache = $this->cachesRepository->fetchOneBy(['cache_id' => $id]);

        return [$this->cachesRepository->getDatabaseArrayFromEntity($fetchedCache)];
    }

    /**
     * @param string $wayPoint
     *
     * @return array
     * @throws RecordNotFoundException
     */
    public function getCacheDetailsByWayPoint(string $wayPoint) : array {
        $fetchedCache = $this->cachesRepository->fetchOneBy(['wp_oc' => $wayPoint]);

        return [$this->cachesRepository->getDatabaseArrayFromEntity($fetchedCache)];
    }
}
