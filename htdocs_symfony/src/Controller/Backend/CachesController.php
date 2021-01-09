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
            $fetchedCaches = $this->getCachesBasicData($connection, $inputData["content_caches_searchfield"]);
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
     * @Route("/cache/{wp_oc}", name="cache_by_wp_oc")
     */
    public function search_by_cache_wp(Connection $connection, string $wp_oc)
    : Response {
        $fetchedCaches = $this->getCachesDetailsData($connection, $wp_oc);

        return $this->render('backend/caches/detailsearch.html.twig', ['cache_by_id' => $fetchedCaches]);
    }

    /**
     *
     */
    function getCachesBasicData(Connection $connection, string $searchtext)
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
            ->orWhere('caches.name LIKE "%' . $searchtext . '%"') // LIKE funktioniert
//            ->orWhere('caches.name LIKE "%:searchTerm%"') // LIKE funktioniert nicht
            ->setParameters(['searchTerm' => $searchtext])
            ->orderBy('caches.wp_oc', 'DESC');
//dd($qb);
//die();

        $result = $qb->execute()->fetchAll();

//dd($result);
//die();

        return $result;
    }

    /**
     *
     */
    function getCachesDetailsData(Connection $connection, string $searchtext)
    : array {
        $fetchedCaches = [];

        if ($searchtext != "") {
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
                ->setParameters(['searchTerm' => $searchtext])
                ->orderBy('caches.wp_oc', 'DESC');
//dd($qb);
//die();

            $fetchedCaches = $qb->execute()->fetchAll();

//dd($fetchedCaches);
//die();

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

        return $fetchedCaches;
    }
}
