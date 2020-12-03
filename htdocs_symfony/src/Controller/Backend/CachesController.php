<?php

declare(strict_types=1);

namespace Oc\Controller\Backend;

use Doctrine\DBAL\Connection;
use Oc\Repository\CachesRepository;
use Oc\Repository\Exception\RecordNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CachesController extends AbstractController
{
    /**
     * @Route("/caches", name="caches_index")
     */
    public function index(Connection $connection): Response
    {
        $caches = $connection->fetchAll('SELECT * FROM caches');

        return $this->render('backend/caches/index.html.twig', array('caches' => $caches));
    }

    /**
     * @Route("/caches/list", name="caches_by_searchfield", methods="POST")
     */
    public function list(Connection $connection, string $searchtext): Response
    {
        $sql_string = '
            SELECT * FROM caches 
            WHERE wp_oc = $searchtext
            OR wp_gc = $searchtext
            OR wp_nc = $searchtext
            OR name LIKE $searchtext
            ';

        $caches = $connection->findAll($sql_string);

        return $this->render('backend/caches/index.html.twig');
    }

    /**
     * @Route("/cache/{wp_oc}", name="cache_by_wp_oc")
     */
    public function cache_id(Connection $connection, string $wp_oc): Response
    {
        echo $wp_oc;
//        die();
        $blubb = 'SELECT * FROM caches WHERE wp_oc= "' . $wp_oc . '"';
        echo $blubb;
//        die();
        $fetched_cache = $connection->fetchAll('SELECT * FROM caches WHERE wp_oc= "' . $wp_oc . '"');

        return $this->render('backend/caches/index.html.twig', ['cache_by_id' => $fetched_cache]);
        return $this->render('backend/caches/index.html.twig');
    }
}
