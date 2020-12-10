<?php

declare(strict_types=1);

namespace Oc\Controller\Backend;

use Doctrine\DBAL\Connection;
use Form\CachesFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CachesController extends AbstractController
{
    /**
     * @Route("/caches", name="caches_index")
     */
    public function index(Connection $connection, Request $request)
    : Response {
        $fetched_caches = '0';

        // create input field for caches_by_searchfield
        $form = $this->createForm(CachesFormType::class);

        // see: https://symfonycasts.com/screencast/symfony-forms/form-submit
        // handles the request (submit-button of the form), but only if there is a POST request
        $form->handleRequest($request);
        // if is true only if there is a request submitted and it is valid
        if ($form->isSubmitted() && $form->isValid()) {
            // read content of form input field
            $input_data = $form->getData();

            // send request to DB
            $fetched_caches = $this->get_caches_basic_data($connection, $input_data["content_caches_searchfield"]);
        }

        return $this->render(
            'backend/caches/index.html.twig', [
                                                'cachesForm' => $form->createView(),
                                                'caches_by_searchfield' => $fetched_caches
                                            ]
        );
    }

    /**
     * @Route("/cache/{wp_oc}", name="cache_by_wp_oc")
     */
    public function search_by_cache_wp(Connection $connection, string $wp_oc)
    : Response {
        $fetched_caches = $this->get_caches_details_data($connection, $wp_oc);

        return $this->render('backend/caches/index.html.twig', ['cache_by_id' => $fetched_caches]);
    }

    /**
     *
     */
    function get_caches_basic_data(Connection $connection, string $searchtext)
    : array {
        $fetched_caches = [];

        if ($searchtext != "") {
            // search in database for the given $searchtext in wp_oc, wp_gc, wp_nc and name
            $fetched_caches = $connection->fetchAll(
                'SELECT cache_id, name, wp_oc, wp_gc, wp_nc FROM caches
                 WHERE wp_oc         =       "' . $searchtext . '"
                 OR caches.wp_gc     =       "' . $searchtext . '"
                 OR caches.wp_nc     =       "' . $searchtext . '"
                 OR caches.name     LIKE    "%' . $searchtext . '%"'
            );
        }

        return $fetched_caches;
    }

    /**
     *
     */
    function get_caches_details_data(Connection $connection, string $searchtext)
    : array {
        $fetched_caches = [];

        if ($searchtext != "") {
            $sql_string = '
            SELECT caches.cache_id, caches.wp_oc, caches.wp_gc, caches.wp_nc, caches.name, 
                   caches.date_hidden, caches.date_created, caches.is_publishdate, caches.latitude, caches.longitude,
                   caches.difficulty, caches.terrain, caches.size, caches.logpw,
                   cache_status.name as cache_status_name, cache_type.icon_large as cache_type_picture, 
                   cache_size.name as cache_size_name, user.username
            FROM caches
            INNER JOIN user ON caches.user_id = user.user_id
            INNER JOIN cache_status ON caches.status = cache_status.id
            INNER JOIN cache_type ON caches.type = cache_type.id
            INNER JOIN cache_size ON caches.size = cache_size.id
            WHERE caches.wp_oc       = "' . $searchtext . '"
                  or caches.wp_gc    = "' . $searchtext . '"
                  or caches.wp_nc    = "' . $searchtext . '"
                  or caches.name LIKE "%' . $searchtext . '%"
            ';

            $fetched_caches = $connection->fetchAll($sql_string);

            $array_size = count($fetched_caches);

            for ($i = 0; $i < $array_size; $i ++) {
                // replace existing log passwords with something different
                // nur der Teil mit den Bilderzuweisungen müsste nochmal überdacht werden..
                if ($fetched_caches[$i]["logpw"] != "") {
                    $fetched_caches[$i]["logpw"] = 1;
                } else {
                    $fetched_caches[$i]["logpw"] = 0;
                }

                // replace cache type information with picture links
                // auch hier müsste die Bildzuweisung nochmal überarbeitet werden..
                $fetched_caches[$i]["cache_type_picture"] =
                    "https://www.opencaching.de/resource2/ocstyle/images/cacheicon/"
                    . $fetched_caches[$i]["cache_type_picture"];
            }
        }

        return $fetched_caches;
    }
}
