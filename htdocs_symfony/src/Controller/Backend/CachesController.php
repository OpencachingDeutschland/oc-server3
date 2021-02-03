<?php

declare(strict_types=1);

namespace Oc\Controller\Backend;

use Doctrine\DBAL\Connection;
use Form\CachesFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;
use Oc\Repository\CachesRepository;
use Oc\Repository\CacheSizeRepository;
use Oc\Repository\CacheStatusRepository;
use Oc\Repository\CacheTypeRepository;
use Oc\Repository\UserRepository;

/**
 * Class CachesController
 *
 * @package Oc\Controller\Backend
 */
class CachesController extends AbstractController
{
    private $connection;

    private $cachesRepository;

    private $cacheSizeRepository;

    private $cacheStatusRepository;

    private $cacheTypeRepository;

    private $userRepository;

    /**
     * CachesController constructor.
     *
     * @param Connection $connection
     * @param CachesRepository $cachesRepository
     * @param CacheSizeRepository $cacheSizeRepository
     * @param CacheStatusRepository $cacheStatusRepository
     * @param CacheTypeRepository $cacheTypeRepository
     * @param UserRepository $userRepository
     */
    public function __construct(
        Connection $connection,
        CachesRepository $cachesRepository,
        CacheSizeRepository $cacheSizeRepository,
        CacheStatusRepository $cacheStatusRepository,
        CacheTypeRepository $cacheTypeRepository,
        UserRepository $userRepository
    ) {
        $this->connection = $connection;
        $this->cachesRepository = $cachesRepository;
        $this->cacheSizeRepository = $cacheSizeRepository;
        $this->cacheStatusRepository = $cacheStatusRepository;
        $this->cacheTypeRepository = $cacheTypeRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @param Request $request
     * @Route("/caches", name="caches_index")
     *
     * @return Response
     */
    public function index(Request $request)
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
            $fetchedCaches = $this->getCachesForSearchField($inputData["content_caches_searchfield"]);
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
    public function search_by_cache_wp(string $wpID) : Response {
        $fetchedCaches = [];

        try {
            $fetchedCaches = $this->getCacheDetails($wpID);
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
        $qb
            ->select('caches.cache_id', 'caches.name', 'caches.wp_oc', 'caches.wp_gc', 'user.username')
            ->from('caches')
            ->innerJoin('caches', 'user', 'user', 'caches.user_id = user.user_id')
            ->where('caches.wp_oc = :searchTerm')
            ->orWhere('caches.wp_gc = :searchTerm')
            ->orWhere('caches.name LIKE :searchTermLIKE')
            ->orWhere('user.username LIKE :searchTermLIKE')
            ->setParameters(['searchTerm' => $searchtext, 'searchTermLIKE' => '%' . $searchtext . '%'])
            ->orderBy('caches.wp_oc', 'ASC');

        $result = $qb->execute()->fetchAll();

        return $result;
    }

    /**
     * @param string $wpID
     * @param int $id
     *
     * @return array
     *
     * getCacheDetails: Suche mittels wp_oc oder cache_id
     */
    public function getCacheDetails(string $wpID = '', int $id = 0)
    : array {
        $fetchedCache = [];

        if (!empty($wpID)) {
            $fetchedCache = $this->cachesRepository->fetchOneBy(['wp_oc' => $wpID]);
        } elseif ($id != 0) {
            $fetchedCache = $this->cachesRepository->fetchOneBy(['cache_id' => $id]);
        }

        if ($fetchedCache) {
            // Ergaenze user
            $fetchedUser = $this->userRepository->fetchOneById($fetchedCache->userId);
            $fetchedCache->user = $fetchedUser;

            // Ergaenze cache_type
            $fetchedCacheType = $this->cacheTypeRepository->fetchOneBy(['id' => $fetchedCache->type]);
            $fetchedCache->cacheType = $fetchedCacheType;

            // Ergaenze caches_size
            $fetchedCacheSize = $this->cacheSizeRepository->fetchOneBy(['id' => $fetchedCache->size]);
            $fetchedCache->cacheSize = $fetchedCacheSize;

            // Ergaenze caches_status
            $fetchedCacheStatus = $this->cacheStatusRepository->fetchOneBy(['id' => $fetchedCache->status]);
            $fetchedCache->cacheStatus = $fetchedCacheStatus;

            // terrain und difficulty / 2
            $fetchedCache->terrain = $fetchedCache->terrain / 2;
            $fetchedCache->difficulty = $fetchedCache->difficulty / 2;

            // Loesche Logpasswort
            if ($fetchedCache->logpw != '') {
                $fetchedCache->logpw = 1;
            }
        }

        return [$this->cachesRepository->getDatabaseArrayFromEntity($fetchedCache)];
    }
}
