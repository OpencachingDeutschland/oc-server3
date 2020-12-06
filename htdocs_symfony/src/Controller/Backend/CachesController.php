<?php

declare(strict_types=1);

namespace Oc\Controller\Backend;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
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
    public function index(Connection $connection, Request $request): Response
    {
        // declare variable to avoid render-error when $request is empty // there is surely a better method..
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
            $fetched_caches = $this->search_by_searchfield($connection, $input_data["content_caches_searchfield"]);

//            dd($input_data["content_caches_searchfield"]);
//            dd($fetched_caches);
//            die();
        }

        // load all caches from database and hand over to twig page
        // just for initial test to learn how it works.. leave it here for later check up
        // $caches = $connection->fetchAll('SELECT * FROM caches');

        return $this->render('backend/caches/index.html.twig', ['cachesForm' => $form->createView(), 'caches_by_searchfield' => $fetched_caches]);
    }

    /**
     * @Route("/caches/caches_by_searchfield", name="create_form_caches_by_searchfield")
     */
    public function create_form_caches_by_searchfield(EntityManagerInterface $em)
    {
        $form = $this->createForm(CachesFormType::class);

        return $this->render('backend/caches/index.html.twig', ['cachesForm' => $form->createView()]);
    }

    /**
     * @Route("/cache/{wp_oc}", name="cache_by_wp_oc")
     */
    public function search_by_cache_id(Connection $connection, string $wp_oc): Response
    {
        $wp_oc = strtoupper($wp_oc);

        // search in database for wp_oc with the given {wp_oc}
        $fetched_cache = $connection->fetchAll('SELECT * FROM caches WHERE wp_oc = "' . $wp_oc . '"');

        // if search for wp_oc gave no result, search for wp_gc
        if (!$fetched_cache) {
            $fetched_cache = $connection->fetchAll('SELECT * FROM caches WHERE wp_gc = "' . $wp_oc . '"');

            // if search for wp_gc also gave no result, search for wp_nc
            if (!$fetched_cache)
                $fetched_cache = $connection->fetchAll('SELECT * FROM caches WHERE wp_nc = "' . $wp_oc . '"');
        }

//        if (!$fetched_cache)
//            print_r("nope, nix da");
//        else
//            print_r($fetched_cache);
//        die();

        return $this->render('backend/caches/index.html.twig', ['cache_by_id' => $fetched_cache]);
    }

//    /**
//     * @Route("/caches/list", name="caches_by_searchfield", methods="POST")
//     */
    function search_by_searchfield(Connection $connection, string $searchtext)
    {

        if ($searchtext != "") {
            $sql_string = '
            SELECT * FROM caches
            WHERE wp_oc = "' . $searchtext . '"
            OR wp_gc = "' . $searchtext . '"
            OR wp_nc = "' . $searchtext . '"
            OR name LIKE "%' . $searchtext . '%"
            ';

            $fetched_caches = $connection->fetchAll($sql_string);
        }

//        print_r("searchtext: " . $searchtext . " /// ");
//        print_r("sql_string: " . $sql_string . " /// ");
//        print_r("fetched_caches: ");
//        dd($fetched_caches);
//        die();

        return $fetched_caches;

    }
}
