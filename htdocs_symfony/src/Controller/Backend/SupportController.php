<?php

declare(strict_types=1);

namespace Oc\Controller\Backend;

use Doctrine\DBAL\Connection;
use Oc\Repository\CachesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SupportController
 *
 * @package Oc\Controller\Backend
 */
class SupportController extends AbstractController
{
    private $connection;

    private $cachesRepository;

    /**
     * SupportController constructor.
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
     * @Route("/support", name="support_index")
     *
     * @return Response
     */
    public function index(Request $request)
    : Response {
        return $this->render('backend/support/index.html.twig', []);
    }

    /**
     * @Route("/reportedCaches", name="support_reported_caches")
     *
     * @return Response
     */
    public function listReportedCaches()
    : Response {
        $fetchedReports = $this->getReportedCaches();

        return $this->render('backend/support/reportedCaches.html.twig', ['reportedCaches_by_id' => $fetchedReports]);
    }

    /**
     *
     * @return array
     */
    public function getReportedCaches()
    : array {
//        $fetchedReports = $this->cachesRepositoryXX->fetchAll();
//
//        return [$this->cachesRepositoryXX->getDatabaseArrayFromEntity($fetchedReports)];
    }

//    /**
//     * @param string $wayPoint
//     *
//     * @return array
//     */
//    public function getCacheDetailsByWayPoint(string $wayPoint)
//    : array {
//        $fetchedCache = $this->cachesRepository->fetchOneBy(['wp_oc' => $wayPoint]);
//
//        return [$this->cachesRepository->getDatabaseArrayFromEntity($fetchedCache)];
//    }
}
