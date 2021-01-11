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
use Oc\Repository\UserRepository;
use Oc\Repository\SecurityRolesRepository;
use Oc\Entity\CachesEntity;

class CachesController extends AbstractController
{
    /**
     * @Route("/caches", name="caches_index")
     */
    public function index(Connection $connection, Request $request)
    : Response {
        $fetchedCaches = '0';

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
            $fetchedCaches = $this->getCachesForSearchField($connection, $inputData["content_caches_searchfield"]);
        }

        if ($fetchedCaches === '0') {
            return $this->render('backend/caches/index.html.twig', ['cachesForm' => $form->createView()]);
        } else {
            return $this->render(
                'backend/caches/basicsearch.html.twig', [
                                                          'cachesForm' => $form->createView(),
                                                          'caches_by_searchfield' => $fetchedCaches
                                                      ]
            );
        }
    }

    /**
     * @Route("/cache/{wpID}", name="cache_by_wp_oc_gc")
     */
    public function search_by_cache_wp(Connection $connection, string $wpID)
    : Response {
        $fetchedCaches = $this->getCacheDetails($connection, $wpID);

        return $this->render('backend/caches/detailsearch.html.twig', ['cache_by_id' => $fetchedCaches]);
    }

    /**
     *
     */
    function getCachesForSearchField(Connection $connection, string $searchtext)
    : array {
        //      so sieht die SQL-Vorlage aus..
        //        SELECT cache_id, name, wp_oc, user.username
        //        FROM caches
        //        INNER JOIN user ON caches.user_id = user.user_id
        //        WHERE wp_oc         =       "' . $searchtext . '"
        //        OR wp_gc            =       "' . $searchtext . '"
        //        OR caches.name     LIKE    "%' . $searchtext . '%"'
        $qb = $connection->createQueryBuilder();
        $qb
            ->select('caches.cache_id', 'caches.name', 'caches.wp_oc', 'caches.wp_gc', 'user.username')
            ->from('caches')
            ->innerJoin('caches', 'user', 'user', 'caches.user_id = user.user_id')
            ->where('caches.wp_oc = :searchTerm')
            ->orWhere('caches.wp_gc = :searchTerm')
            ->orWhere('caches.name LIKE :searchTermLIKE')
            ->setParameters(['searchTerm' => $searchtext, 'searchTermLIKE' => '%' . $searchtext . '%'])
            ->orderBy('caches.wp_oc', 'ASC');

        $result = $qb->execute()->fetchAll();

        return $result;
    }

    /**
     * getCacheDetails_Nr3: Suche mittels Suchtext (Wegpunkte, Cachename) oder cache_id
     * Grundidee: diese Funktion holt sich die Daten über die fetch-Funktionen im Repository und
     *            ergänzt weitere Angaben aus anderen Tabellen, indem in den Entitys Beziehungen zwischen den Tabellen
     *            geknüpft werden (ManyToOne, OneToMany, ..)
     * Das konnte ich aber nicht wie gewollt implementieren.
     */
    function getCacheDetails_Nr3(Connection $connection, string $wpID = "", int $cacheId = 0)
    : array {
        $fetchedCaches = [];
//        $fetchedCache = [];
//
//        if ($cacheId != 0) {
//            $request = new CachesRepository($connection);
//
//            $fetchedCache = $request->fetchOneBy(['cache_id' => $cacheId]);
//
//            if ($fetchedCache) {
//                $wpID = $fetchedCache->getOCid();
//            }
//        }
//
//        if ($wpID != "") {
//            $request = new CachesRepository($connection);
//            $fetchedCache = $request->fetchOneBy(['wp_OC' => $wpID]);
//
//            if ($fetchedCache) {
//                $fetchedCaches = $fetchedCache->convertEntityToArray();
//            }
//        }

//        dd($fetchedCaches);
//        die();

        return $fetchedCaches;
    }

    /**
     * getCacheDetails_Nr2: Suche mittels der cache_id
     * Alternative zu getCacheDetails() ??
     */
    function getCacheDetails_Nr2(Connection $connection, int $cacheId)
    : array {
        $fetchedCache = [];
        $securityRolesRepository = new SecurityRolesRepository($connection);

        if ($cacheId != 0) {
            $requestCache = new CachesRepository($connection);
            $fetchedCache = $requestCache->fetchOneBy(['cache_id' => $cacheId]);

            if ($fetchedCache) {
                // ersetze caches.user_id mit user.username
                $requestUser = new UserRepository($connection, $securityRolesRepository);
                $fetchedUser = $requestUser->fetchOneById(intval($fetchedCache->getUserId()));

                $fetchedCache->setUserId($fetchedUser->getUsername());

                // ersetze caches.status mit cache_status.name
                // ..

                // ersetze caches.type mit cache_type.name
                // ..

                // ersetze caches.size mit cache_size.name
                // ..

                // ?? ..
            }
        }

        dd($fetchedCache);
        die();

        return $fetchedCache;
    }

    /**
     * getCacheDetails: Suche mittels Suchtext (Wegpunkte, Cachename) oder cache_id
     * Grundidee: diese Funktion baut sich die SQL-Anweisung selbst zusammen und holt die Daten aus der DB.
     */
    function getCacheDetails(Connection $connection, string $searchText = "", int $cacheId = 0)
    : array {
        $fetchedCaches = [];

        if ($cacheId != 0) {
            $request = new CachesRepository($connection);

            $fetchedCache = $request->fetchOneBy(['cache_id' => $cacheId]);

            if ($fetchedCache) {
                $searchText = $fetchedCache->getOCid();
            }
        }

        if ($searchText != "") {
            //      so sieht die SQL-Vorlage aus..
            //            SELECT caches.cache_id, caches.name, caches.wp_oc, caches.wp_gc,
            //                   caches.date_hidden, caches.date_created, caches.is_publishdate, caches.latitude, caches.longitude,
            //                   caches.difficulty, caches.terrain, caches.size, caches.logpw,
            //                   cache_status.name as cache_status_name, cache_type.icon_large as cache_type_picture,
            //                   cache_size.name as cache_size_name, user.username
            //            FROM caches
            //            INNER JOIN user ON caches.user_id = user.user_id
            //            INNER JOIN cache_status ON caches.status = cache_status.id
            //            INNER JOIN cache_type ON caches.type = cache_type.id
            //            INNER JOIN cache_size ON caches.size = cache_size.id
            //            WHERE caches.wp_oc         = "' . $searchtext . '"
            $qb = $connection->createQueryBuilder();
            $qb
                ->select('caches.cache_id', 'caches.name', 'caches.wp_oc', 'caches.wp_gc')
                ->addSelect('caches.date_hidden', 'caches.date_created', 'caches.is_publishdate', 'caches.latitude', 'caches.longitude')
                ->addSelect('caches.difficulty', 'caches.terrain', 'caches.size', 'caches.logpw')
                ->addSelect('cache_status.name as cache_status_name', 'cache_type.icon_large as cache_type_picture')
                ->addSelect('cache_size.name as cache_size_name', 'user.username')
                ->from('caches')
                ->innerJoin('caches', 'user', 'user', 'caches.user_id = user.user_id')
                ->innerJoin('caches', 'cache_status', 'cache_status', 'caches.status = cache_status.id')
                ->innerJoin('caches', 'cache_type', 'cache_type', 'caches.type = cache_type.id')
                ->innerJoin('caches', 'cache_size', 'cache_size', 'caches.size = cache_size.id')
                ->where('caches.wp_oc = :searchTerm')
                ->setParameters(['searchTerm' => $searchText])
                ->orderBy('caches.wp_oc', 'DESC');

            $fetchedCaches = $qb->execute()->fetchAll();

            $array_size = count($fetchedCaches);
            for ($i = 0; $i < $array_size; $i ++) {
                // replace existing log passwords with something different
                // nur der Teil mit den Bilderzuweisungen müsste nochmal überdacht werden..
                if ($fetchedCaches[$i]["logpw"] != "") {
                    $fetchedCaches[$i]["logpw"] = 1;
                } else {
                    $fetchedCaches[$i]["logpw"] = 0;
                }

                // replace cache type information with picture links
                // auch hier müsste die Bildzuweisung nochmal überarbeitet werden..
                $fetchedCaches[$i]["cache_type_picture"] =
                    "https://www.opencaching.de/resource2/ocstyle/images/cacheicon/"
                    . $fetchedCaches[$i]["cache_type_picture"];
            }
        }

        //dd($fetchedCaches);
        //die();

        return $fetchedCaches;
    }
}
