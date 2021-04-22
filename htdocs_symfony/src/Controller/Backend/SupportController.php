<?php

declare(strict_types=1);

namespace Oc\Controller\Backend;

use Doctrine\DBAL\Connection;
use Oc\Form\SupportSQLFlexForm;
use Oc\Repository\CacheReportsRepository;
use Oc\Repository\CacheStatusModifiedRepository;
use Oc\Repository\CacheStatusRepository;
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
    /** @var Connection */
    private $connection;

    /** @var CacheReportsRepository */
    private $cacheReportsRepository;

    /** @var CacheStatusModifiedRepository */
    private $cacheStatusModifiedRepository;

    /** @var CacheStatusRepository */
    private $cacheStatusRepository;

    /**
     * SupportController constructor.
     *
     * @param Connection $connection
     * @param CacheReportsRepository $cacheReportsRepository
     * @param CacheStatusModifiedRepository $cacheStatusModifiedRepository
     * @param CacheStatusRepository $cacheStatusRepository
     */
    public function __construct(
        Connection $connection,
        CacheReportsRepository $cacheReportsRepository,
        CacheStatusModifiedRepository $cacheStatusModifiedRepository,
        CacheStatusRepository $cacheStatusRepository
    ) {
        $this->connection = $connection;
        $this->cacheReportsRepository = $cacheReportsRepository;
        $this->cacheStatusModifiedRepository = $cacheStatusModifiedRepository;
        $this->cacheStatusRepository = $cacheStatusRepository;
    }

    /**
     * @return Response
     * @Route("/support", name="support_index")
     */
    public function index()
    : Response
    {
        return $this->render('backend/support/index.html.twig');
    }

    /**
     * @return Response
     * @Route("/supportSearch", name="support_search")
     */
    public function serchCachesAndUser()
    : Response
    {
        return $this->render('backend/support/KOMMTDEMNAECHST.html.twig');
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
     * @param Request $request
     *
     * @return Response
     * @Route("/dbQueries", name="support_db_queries")
     */
    public function listDbQueries(Request $request)
    : Response {
        $fetchedInformation = [];

        $form = $this->createForm(SupportSQLFlexForm::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $inputData = $form->getData();

            $fetchedInformation = $this->executeSQL_flexible($inputData['content_WHAT'], $inputData['content_TABLE']);

            for ($i = 0; $i < count($fetchedInformation); $i ++) {
                if (array_key_exists('password', $fetchedInformation[$i])) {
                    $fetchedInformation[$i]['password'] = '-';
                }
                if (array_key_exists('admin_password', $fetchedInformation[$i])) {
                    $fetchedInformation[$i]['admin_password'] = '-';
                }
            }
            //            dd($fetchedInformation);
            //            die();
        }

        return $this->render(
            'backend/support/databaseQueries.html.twig', ['SQLFlexForm' => $form->createView(), 'suppSQLqueryFlex' => $fetchedInformation]
        );
    }

    /**
     * @param string $repID
     *
     * @return Response
     * @throws \Oc\Repository\Exception\RecordNotFoundException
     * @throws \Oc\Repository\Exception\RecordsNotFoundException
     * @Route("/repCaches/{repID}", name="support_reported_cache")
     */
    public function list_reported_cache_details(string $repID)
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
        return $this->cacheReportsRepository->fetchAll();
    }

    /**
     * @param int $days
     *
     * @return Response
     * @Route("/dbQueries1/{days}", name="support_db_queries_1")
     */
    public function executeSQL_caches_old_reg_date(int $days = 31) // List caches from users whose registration date is not older than x days.
    : Response
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('caches.name', 'user.username', 'user.date_created', 'user.last_login')
            ->from('caches')
            ->innerJoin('caches', 'user', 'user', 'caches.user_id = user.user_id')
            ->where('user.date_created > now() - interval :searchTerm DAY')
            ->andWhere('caches.user_id = user.user_id')
            ->setParameters(['searchTerm' => $days])
            ->orderBy('date_created', 'DESC');

        return $this->render('backend/support/databaseQueries.html.twig', ['suppSQLquery1' => $qb->execute()->fetchAll()]);
    }

    /**
     * @param int $days
     *
     * @return Response
     * @Route("/dbQueries2/{days}", name="support_db_queries_2")
     */
    public function executeSQL_old_reg_date(int $days) // List user whose registration date is no older than x days.
    : Response
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('username', 'date_created', 'last_login')
            ->from('user')
            ->where('date_created > now() - interval :searchTerm DAY')
            ->setParameters(['searchTerm' => $days])
            ->orderBy('date_created', 'DESC');

        return $this->render('backend/support/databaseQueries.html.twig', ['suppSQLquery2' => $qb->execute()->fetchAll()]);
    }

    /**
     * @return Response
     * @Route("/dbQueries4", name="support_db_queries_4")
     */
    public function executeSQL_caches_old_login_date(
    ) // List (non-archived, non-locked) caches from users whose last login date is older than one year.
    : Response
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('caches.name', 'caches.cache_id', 'caches.status', 'user.username', 'user.last_login')
            ->from('caches')
            ->innerJoin('caches', 'user', 'user', 'caches.user_id = user.user_id')
            ->where('user.last_login < now() - interval :searchTerm YEAR')
            ->andWhere('caches.status <= 2')
            ->andWhere(('caches.user_id = user.user_id'))
            ->setParameters(['searchTerm' => 1])
            ->orderBy('user.last_login', 'ASC');

        return $this->render('backend/support/databaseQueries.html.twig', ['suppSQLquery4' => $qb->execute()->fetchAll()]);
    }

    /**
     * @param string $what
     * @param string $table
     *
     * @return array
     */
    public function executeSQL_flexible(string $what, string $table)
    : array {
        $qb = $this->connection->createQueryBuilder();
        $qb->select($what)
            ->from($table);

        return ($qb->execute()->fetchAll());
    }
}
