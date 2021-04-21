<?php

declare(strict_types=1);

namespace Oc\Controller\Backend;

//use Doctrine\DBAL\Connection;
//use Oc\Repository\CachesRepository;
use Oc\Repository\CacheReportsRepository;
use Oc\Repository\CacheStatusModifiedRepository;
use Oc\Repository\CacheStatusRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

//use Oc\Repository\UserRepository;

/**
 * Class SupportController
 *
 * @package Oc\Controller\Backend
 */
class SupportController extends AbstractController
{
    private $cacheReportsRepository;

    private $cacheStatusModifiedRepository;

    private $cacheStatusRepository;

    /**
     * SupportController constructor.
     *
     * @param CacheReportsRepository $cacheReportsRepository
     * @param CacheStatusModifiedRepository $cacheStatusModifiedRepository
     * @param CacheStatusRepository $cacheStatusRepository
     */
    public function __construct(
        CacheReportsRepository $cacheReportsRepository,
        CacheStatusModifiedRepository $cacheStatusModifiedRepository,
        CacheStatusRepository $cacheStatusRepository
    ) {
        $this->cacheReportsRepository = $cacheReportsRepository;
        $this->cacheStatusModifiedRepository = $cacheStatusModifiedRepository;
        $this->cacheStatusRepository = $cacheStatusRepository;
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
     * @return Response
     * @throws \Oc\Repository\Exception\RecordsNotFoundException
     * @Route("/reportedCaches", name="support_reported_caches")
     */
    public function listReportedCaches()
    : Response
    {
        $fetchedReports = $this->getReportedCaches();

        return $this->render('backend/support/reportedCaches.html.twig', ['reportedCaches_by_id' => $fetchedReports]);
    }

    /**
     * @param string $repID
     *
     * @return array
     * @Route("/repCaches/{repID}", name="support_reported_cache")
     */
    public function search_by_report_id(string $repID)
    : Response {
        $fetchedReport = $this->cacheReportsRepository->fetchOneBy(['id' => $repID]);

        $fetchedStatus = $this->cacheStatusRepository->fetchAll();

        $fetchedStatusModfied = $this->cacheStatusModifiedRepository->fetchBy(['cache_id' => $fetchedReport->cacheid]);

        return $this->render(
            'backend/support/reportedCacheDetails.html.twig', [
                                                                'reported_cache_by_id' => $fetchedReport,
                                                                'cache_status' => $fetchedStatus,
                                                                'report_status_modified' => $fetchedStatusModfied
                                                            ]
        );
    }

    /**
     * @return array
     * @throws \Oc\Repository\Exception\RecordsNotFoundException
     */
    public function getReportedCaches()
    : array
    {
        $fetchedReports = $this->cacheReportsRepository->fetchAll();

        return $fetchedReports;
    }
}
