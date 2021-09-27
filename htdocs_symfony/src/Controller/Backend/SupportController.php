<?php

declare(strict_types=1);

namespace Oc\Controller\Backend;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Oc\Entity\SupportListingCommentsEntity;
use Oc\Entity\SupportUserCommentsEntity;
use Oc\Form\SupportCommentField;
use Oc\Form\SupportImportGPX;
use Oc\Form\SupportSearchCaches;
use Oc\Form\SupportSQLFlexForm;
use Oc\Form\SupportUserAccountDetails;
use Oc\Repository\CacheAdoptionsRepository;
use Oc\Repository\CacheCoordinatesRepository;
use Oc\Repository\CacheLogsArchivedRepository;
use Oc\Repository\CacheReportsRepository;
use Oc\Repository\CachesRepository;
use Oc\Repository\CacheStatusModifiedRepository;
use Oc\Repository\CacheStatusRepository;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;
use Oc\Repository\NodesRepository;
use Oc\Repository\SupportBonuscachesRepository;
use Oc\Repository\SupportListingCommentsRepository;
use Oc\Repository\SupportListingInfosRepository;
use Oc\Repository\SupportUserCommentsRepository;
use Oc\Repository\SupportUserRelationsRepository;
use Oc\Repository\UserRepository;
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

    /** @var CacheAdoptionsRepository */
    private $cacheAdoptionsRepository;

    /** @var CacheCoordinatesRepository */
    private $cacheCoordinatesRepository;

    /** @var CacheLogsArchivedRepository */
    private $cacheLogsArchivedRepository;

    /** @var CachesRepository */
    private $cachesRepository;

    /** @var CacheReportsRepository */
    private $cacheReportsRepository;

    /** @var CacheStatusModifiedRepository */
    private $cacheStatusModifiedRepository;

    /** @var CacheStatusRepository */
    private $cacheStatusRepository;

    /** @var NodesRepository */
    private $nodesRepository;

    /** @var SupportBonuscachesRepository */
    private $supportBonuscachesRepository;

    /** @var SupportListingCommentsRepository */
    private $supportListingCommentsRepository;

    /** @var SupportListingInfosRepository */
    private $supportListingInfosRepository;

    /** @var SupportUserCommentsRepository */
    private $supportUserCommentsRepository;

    /** @var SupportUserRelationsRepository */
    private $supportUserRelationsRepository;

    /** @var UserRepository */
    private $userRepository;

    /**
     * @param Connection $connection
     * @param CacheAdoptionsRepository $cacheAdoptionsRepository
     * @param CacheCoordinatesRepository $cacheCoordinatesRepository
     * @param CacheLogsArchivedRepository $cacheLogsArchivedRepository
     * @param CachesRepository $cachesRepository
     * @param CacheReportsRepository $cacheReportsRepository
     * @param CacheStatusModifiedRepository $cacheStatusModifiedRepository
     * @param CacheStatusRepository $cacheStatusRepository
     * @param NodesRepository $nodesRepository
     * @param SupportBonuscachesRepository $supportBonuscachesRepository
     * @param SupportListingCommentsRepository $supportListingCommentsRepository
     * @param SupportListingInfosRepository $supportListingInfosRepository
     * @param SupportUserCommentsRepository $supportUserCommentsRepository
     * @param SupportUserRelationsRepository $supportUserRelationsRepository
     * @param UserRepository $userRepository
     */
    public function __construct(
        Connection $connection,
        CacheAdoptionsRepository $cacheAdoptionsRepository,
        CacheCoordinatesRepository $cacheCoordinatesRepository,
        CacheLogsArchivedRepository $cacheLogsArchivedRepository,
        CachesRepository $cachesRepository,
        CacheReportsRepository $cacheReportsRepository,
        CacheStatusModifiedRepository $cacheStatusModifiedRepository,
        CacheStatusRepository $cacheStatusRepository,
        NodesRepository $nodesRepository,
        SupportBonuscachesRepository $supportBonuscachesRepository,
        SupportListingCommentsRepository $supportListingCommentsRepository,
        SupportListingInfosRepository $supportListingInfosRepository,
        SupportUserCommentsRepository $supportUserCommentsRepository,
        SupportUserRelationsRepository $supportUserRelationsRepository,
        UserRepository $userRepository
    ) {
        $this->connection = $connection;
        $this->cacheAdoptionsRepository = $cacheAdoptionsRepository;
        $this->cacheCoordinatesRepository = $cacheCoordinatesRepository;
        $this->cacheLogsArchivedRepository = $cacheLogsArchivedRepository;
        $this->cachesRepository = $cachesRepository;
        $this->cacheReportsRepository = $cacheReportsRepository;
        $this->cacheStatusModifiedRepository = $cacheStatusModifiedRepository;
        $this->cacheStatusRepository = $cacheStatusRepository;
        $this->nodesRepository = $nodesRepository;
        $this->supportBonuscachesRepository = $supportBonuscachesRepository;
        $this->supportListingCommentsRepository = $supportListingCommentsRepository;
        $this->supportListingInfosRepository = $supportListingInfosRepository;
        $this->supportUserCommentsRepository = $supportUserCommentsRepository;
        $this->supportUserRelationsRepository = $supportUserRelationsRepository;
        $this->userRepository = $userRepository;
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
     * @param Request $request
     *
     * @return Response
     * @Route("/supportSearch", name="support_search")
     */
    public function searchCachesAndUser(Request $request)
    : Response {
        $fetchedCaches = '';
        $limit = false;

        $formSearch = $this->createForm(SupportSearchCaches::class);

        $formSearch->handleRequest($request);
        if ($formSearch->isSubmitted() && $formSearch->isValid()) {
            $inputData = $formSearch->getData();

            if ($formSearch->getClickedButton() === $formSearch->get('search_One')) {
                $limit = true;
            }

            $fetchedCaches = $this->getCachesForSearchField($inputData['content_support_searchfield'], $limit);
        }

        return $this->render(
            'backend/support/searchedCaches.html.twig', [
                                                          'supportCachesForm' => $formSearch->createView(),
                                                          'foundCaches' => $fetchedCaches
                                                      ]
        );
    }

    /**
     * @return Response
     * @throws RecordNotFoundException
     * @throws RecordsNotFoundException
     * @Route("/reportedCaches", name="support_reported_caches")
     */
    public function listReportedCaches()
    : Response
    {
        $fetchedReports = $this->getReportedCaches();

        $formSearch = $this->createForm(SupportSearchCaches::class);

        return $this->render(
            'backend/support/reportedCaches.html.twig', [
                                                          'supportCachesForm' => $formSearch->createView(),
                                                          'reportedCaches_by_id' => $fetchedReports
                                                      ]
        );
    }

    /**
     * @return Response
     *
     * @Route("/bonusCaches", name="support_bonus_caches")
     */
    public function listBonusCaches()
    : Response
    {
        $fetchedBonuscaches = $this->getBonusCaches();

        $formSearch = $this->createForm(SupportSearchCaches::class);

        return $this->render(
            'backend/support/bonusCaches.html.twig', [
                                                       'supportCachesForm' => $formSearch->createView(),
                                                       'bonusCaches_by_id' => $fetchedBonuscaches
                                                   ]
        );
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

        $formSearch = $this->createForm(SupportSearchCaches::class);

        $form = $this->createForm(SupportSQLFlexForm::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $inputData = $form->getData();

            $fetchedInformation = $this->executeSQL_flexible($inputData['content_WHAT'], $inputData['content_TABLE']);

            $countFetched = count($fetchedInformation);
            for ($i = 0; $i < $countFetched; $i ++) {
                if (array_key_exists('password', $fetchedInformation[$i])) {
                    $fetchedInformation[$i]['password'] = '-';
                }
                if (array_key_exists('logpw', $fetchedInformation[$i])) {
                    $fetchedInformation[$i]['logpw'] = '-';
                }
                if (array_key_exists('admin_password', $fetchedInformation[$i])) {
                    $fetchedInformation[$i]['admin_password'] = '-';
                }
            }
        }

        return $this->render(
            'backend/support/databaseQueries.html.twig', [
                                                           'supportCachesForm' => $formSearch->createView(),
                                                           'SQLFlexForm' => $form->createView(),
                                                           'suppSQLqueryFlex' => $fetchedInformation
                                                       ]
        );
    }

    /**
     * @param string $wpID
     *
     * @return Response
     * @throws RecordNotFoundException
     * @throws RecordsNotFoundException
     * @Route("/cacheHistory/{wpID}", name="support_cache_history")
     */
    public function list_cache_history(string $wpID)
    : Response {
        $formSearch = $this->createForm(SupportSearchCaches::class);

        $fetchedId = $this->cachesRepository->getIdByWP($wpID);

        $fetchedReports = $this->cacheReportsRepository->fetchBy(['cacheid' => $fetchedId]);

        $fetchedLogDeletes = $this->cacheLogsArchivedRepository->fetchBy(['cache_id' => $fetchedId]);

        $fetchedStatusModfied = $this->cacheStatusModifiedRepository->fetchBy(['cache_id' => $fetchedId]);

        $fetchedCoordinates = $this->cacheCoordinatesRepository->fetchBy(['cache_id' => $fetchedId]);

        $fetchedAdoptions = $this->cacheAdoptionsRepository->fetchBy(['cache_id' => $fetchedId]);

        return $this->render(
            'backend/support/cacheHistory.html.twig', [
                                                        'supportCachesForm' => $formSearch->createView(),
                                                        'cache_reports' => $fetchedReports,
                                                        'deleted_logs' => $fetchedLogDeletes,
                                                        'report_status_modified' => $fetchedStatusModfied,
                                                        'changed_coordinates' => $fetchedCoordinates,
                                                        'cache_adoptions' => $fetchedAdoptions,
                                                    ]
        );
    }

    /**
     * @param int $repID
     *
     * @return Response
     * @throws RecordNotFoundException
     * @throws RecordsNotFoundException
     * @Route("/repCaches/{repID}", name="support_reported_cache")
     */
    public function list_reported_cache_details(int $repID)
    : Response {
        $formSearch = $this->createForm(SupportSearchCaches::class);
        $formComment = $this->createForm(SupportCommentField::class);

        $fetchedReport = $this->cacheReportsRepository->fetchOneBy(['id' => $repID]);

        $fetchedStatus = $this->cacheStatusRepository->fetchAll();

        $fetchedStatusModfied = $this->cacheStatusModifiedRepository->fetchBy(['cache_id' => $fetchedReport->cacheid]);

        return $this->render(
            'backend/support/reportedCacheDetails.html.twig', [
                                                                'supportCachesForm' => $formSearch->createView(),
                                                                'supportAdminCommentForm' => $formComment->createView(),
                                                                'reported_cache_by_id' => $fetchedReport,
                                                                'cache_status' => $fetchedStatus,
                                                                'report_status_modified' => $fetchedStatusModfied
                                                            ]
        );
    }

    /**
     * @param string $wpID
     * @param int $userID
     *
     * @return Response
     * @throws DBALException
     * @throws RecordNotFoundException
     * @throws \Oc\Repository\Exception\RecordAlreadyExistsException
     *
     * @Route("/occ/{wpID}&{userID}", name="support_occ")
     */
    public function occPage(string $wpID, int $userID)
    : Response {
        $formCommentUser = $this->createForm(SupportCommentField::class);
        $formCommentCache = $this->createForm(SupportCommentField::class);

        $fetchedCacheData = [];
        $fetchedCacheComments = [];
        $fetchedCacheInfos = [];
        $fetchedUserRelations = [];

        // Die OCC-Seite kann auch ohne Angabe einer Cache-ID und nur mit einer User-ID aufgerufen werden.
        if ($wpID != '0') {
            // Basisccachedaten abholen
            $fetchedCacheData = $this->cachesRepository->fetchOneBy(['wp_oc' => $wpID]);
            // Supportkommentar zum Cache abholen. Ggf. neuen, leeren anlegen.
            try {
                $fetchedCacheComments = $this->supportListingCommentsRepository->fetchOneBy(['wp_oc' => $wpID]);
            } catch (\Exception $exception) {
                $entity = new SupportListingCommentsEntity($wpID);
                $fetchedCacheComments = $this->supportListingCommentsRepository->create($entity);
            }
            // Cachedaten zu Fremnodes abholen (es können mehrere Einträge in der DB existieren)
            try {
                $fetchedCacheInfos = $this->supportListingInfosRepository->fetchBy(['wp_oc' => $wpID]);
            } catch (\Exception $exception) {
            }
        }

        // Basisnutzerdaten abolen
        $fetchedUserData = $this->userRepository->fetchOneById($userID);
        // Supportkommentar zum Nutzer abolen. Ggf. neuen, leeren anlegen.
        try {
            $fetchedUserComments = $this->supportUserCommentsRepository->fetchOneBy(['oc_user_id' => $userID]);
        } catch (\Exception $exception) {
            $entity = new SupportUserCommentsEntity($userID);
            $fetchedUserComments = $this->supportUserCommentsRepository->create($entity);
        }
        // Nutzerdaten zu Fremnodes abholen (es können mehrere Einträge in der DB existieren)
        try {
            $fetchedUserRelations = $this->supportUserRelationsRepository->fetchBy(['oc_user_id' => $userID]);
        } catch (\Exception $exception) {
        }

        $formSearch = $this->createForm(SupportSearchCaches::class);

        return $this->render(
            'backend/support/occ.html.twig', [
                                               'supportCachesForm' => $formSearch->createView(),
                                               'supportCommentFormUser' => $formCommentUser->createView(),
                                               'supportCommentFormCache' => $formCommentCache->createView(),
                                               'occ_cache_data' => $fetchedCacheData,
                                               'occ_cache_comments' => $fetchedCacheComments,
                                               'occ_cache_infos' => $fetchedCacheInfos,
                                               'occ_user_data' => $fetchedUserData,
                                               'occ_user_comments' => $fetchedUserComments,
                                               'occ_user_relations' => $fetchedUserRelations
                                           ]
        );
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @throws DBALException
     * @throws RecordNotFoundException
     * @throws RecordNotPersistedException
     * @Route("/occSaveText", name="support_occ_save_text")
     */
    public function occ_saveTextArea(Request $request)
    : Response {
        $form = $this->createForm(SupportCommentField::class)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $inputData = $form->getData();

            if ($inputData['hidden_sender'] == 'textfield_cache_comment') {
                $entity = $this->supportListingCommentsRepository->fetchOneBy(['wp_oc' => (string) $inputData['hidden_ID2']]);
                $entity->comment = $inputData['content_comment_field'];
                $this->supportListingCommentsRepository->update($entity);
            } elseif ($inputData['hidden_sender'] == 'textfield_user_comment') {
                $entity = $this->supportUserCommentsRepository->fetchOneBy(['oc_user_id' => (int) $inputData['hidden_ID1']]);
                $entity->comment = $inputData['content_comment_field'];
                $this->supportUserCommentsRepository->update($entity);
            }

            return $this->redirectToRoute('backend_support_occ', [
                'userID' => (string) $inputData['hidden_ID1'],
                'wpID' => (string) $inputData['hidden_ID2']
            ]);
        }

        return $this->redirectToRoute('backend_support_occ');
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @throws DBALException
     * @throws RecordNotFoundException
     * @throws RecordNotPersistedException
     * @Route("/repCachesSaveText", name="support_reported_cache_save_text")
     */
    public function repCaches_saveTextArea(Request $request)
    : Response {
        $form = $this->createForm(SupportCommentField::class)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $inputData = $form->getData();

            $entity = $this->cacheReportsRepository->fetchOneBy(['id' => (int) $inputData['hidden_ID1']]);
            $entity->comment = $inputData['content_comment_field'];

            $this->cacheReportsRepository->update($entity);

            return $this->redirectToRoute('backend_support_reported_cache', ['repID' => $entity->id]);
        }

        return $this->redirectToRoute('backend_support_reported_caches');
    }

    /**
     * @param int $repID
     * @param int $adminId
     * @param string $route
     *
     * @return Response
     * @throws DBALException
     * @throws RecordNotFoundException
     * @throws RecordNotPersistedException
     * @route("/repCachesAssignSupportuser/{repID}&{adminId}&{route}", name="support_reported_cache_supportuser_assignment")
     */
    public function repCaches_supportuser_assignment(int $repID, int $adminId, string $route)
    : Response {
        $entity = $this->cacheReportsRepository->fetchOneBy(['id' => $repID]);
        $entity->adminid = $adminId;

        $this->cacheReportsRepository->update($entity);

        return $this->redirectToRoute($route, ['repID' => $repID]);
    }

    /**
     * @param int $repID
     * @param string $route
     *
     * @return Response
     * @throws DBALException
     * @throws RecordNotFoundException
     * @throws RecordNotPersistedException
     * @route("/repCachesAssignSupportuser/{repID}&{route}", name="support_reported_cache_set_status")
     */
    public function repCaches_setReportStatus(int $repID, string $route)
    : Response {
        $entity = $this->cacheReportsRepository->fetchOneBy(['id' => $repID]);
        $entity->status = 3; // ToDo: die '3' hart vorgeben? Oder wie?

        $this->cacheReportsRepository->update($entity);

        return $this->redirectToRoute($route, ['repID' => $repID]);
    }

    /**
     * @param int $userID
     *
     * @return Response
     * @throws RecordNotFoundException
     *
     * @Route("/uad/{userID}", name="support_user_account_details")
     */
    public function user_account_details_Page(int $userID)
    : Response {
        $fetchedUserDetails = $this->userRepository->fetchOneById($userID);

        $formSearch = $this->createForm(SupportSearchCaches::class);
        $formActions = $this->createForm(SupportUserAccountDetails::class);

        return $this->render(
            'backend/support/userDetails.html.twig', [
                                                       'supportCachesForm' => $formSearch->createView(),
                                                       'supportUserAccountActions' => $formActions->createView(),
                                                       'user_account_details' => $fetchedUserDetails
                                                   ]
        );
    }

    /**
     * @param string $searchtext
     * @param bool $limit
     *
     * @return array
     */
    public function getCachesForSearchField(string $searchtext, bool $limit = false)
    : array {
        //      so sieht die SQL-Vorlage aus..
        //        SELECT name, wp_oc, wp_gc, wp_gc_maintained, user.username, user.email
        //        FROM caches
        //        INNER JOIN user ON caches.user_id = user.user_id
        //        WHERE wp_oc         =       "' . $searchtext . '"
        //        OR wp_gc            =       "' . $searchtext . '"
        //        OR wp_gc_maintained =       "' . $searchtext . '"
        //        OR caches.name     LIKE    "%' . $searchtext . '%"'
        //        OR user.username   LIKE    "%' . $searchtext . '%"'
        //        OR user.email      LIKE    "%' . $searchtext . '%"'
        //        LIMIT $limit
        $qb = $this->connection->createQueryBuilder();
        $qb->select('caches.name', 'caches.wp_oc', 'caches.wp_gc', 'caches.wp_gc_maintained', 'user.user_id', 'user.username', 'user.email')
            ->from('caches')
            ->innerJoin('caches', 'user', 'user', 'caches.user_id = user.user_id')
            ->where('caches.wp_oc = :searchTerm')
            ->orWhere('caches.wp_gc = :searchTerm')
            ->orWhere('caches.wp_gc_maintained = :searchTerm')
            ->orWhere('caches.name LIKE :searchTermLIKE')
            ->orWhere('user.username LIKE :searchTermLIKE')
            ->orWhere('user.email LIKE :searchTermLIKE')
            ->setParameters(['searchTerm' => $searchtext, 'searchTermLIKE' => '%' . $searchtext . '%'])
            ->orderBy('caches.wp_oc', 'ASC');

        if ($limit === true) {
            $qb->setMaxResults(1);
        }

        return $qb->execute()->fetchAll();
    }

    /**
     * @return array
     */
    public function getBonusCaches()
    : array
    {
        try {
            $fetchedBonusCaches = $this->supportBonuscachesRepository->fetchAll();

            return $fetchedBonusCaches;
        } catch (\Exception $exception) {
            return [];
        }
    }

    /**
     * @return array
     * @throws RecordNotFoundException
     * @throws RecordsNotFoundException
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
        $formSearch = $this->createForm(SupportSearchCaches::class);

        $qb = $this->connection->createQueryBuilder();
        $qb->select('caches.name', 'user.username', 'user.date_created', 'user.last_login')
            ->from('caches')
            ->innerJoin('caches', 'user', 'user', 'caches.user_id = user.user_id')
            ->where('user.date_created > now() - interval :searchTerm DAY')
            ->andWhere('caches.user_id = user.user_id')
            ->setParameters(['searchTerm' => $days])
            ->orderBy('date_created', 'DESC');

        return $this->render(
            'backend/support/databaseQueries.html.twig', [
                                                           'supportCachesForm' => $formSearch->createView(),
                                                           'suppSQLquery1' => $qb->execute()->fetchAll()
                                                       ]
        );
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
        $formSearch = $this->createForm(SupportSearchCaches::class);

        $qb = $this->connection->createQueryBuilder();
        $qb->select('username', 'date_created', 'last_login')
            ->from('user')
            ->where('date_created > now() - interval :searchTerm DAY')
            ->setParameters(['searchTerm' => $days])
            ->orderBy('date_created', 'DESC');

        return $this->render(
            'backend/support/databaseQueries.html.twig', [
                                                           'supportCachesForm' => $formSearch->createView(),
                                                           'suppSQLquery2' => $qb->execute()->fetchAll()
                                                       ]
        );
    }

    /**
     * @return Response
     * @Route("/dbQueries4", name="support_db_queries_4")
     */
    public function executeSQL_caches_old_login_date(
    ) // List (non-archived, non-locked) caches from users whose last login date is older than one year.
    : Response
    {
        $formSearch = $this->createForm(SupportSearchCaches::class);

        $qb = $this->connection->createQueryBuilder();
        $qb->select('caches.name', 'caches.cache_id', 'caches.status', 'user.username', 'user.last_login')
            ->from('caches')
            ->innerJoin('caches', 'user', 'user', 'caches.user_id = user.user_id')
            ->where('user.last_login < now() - interval :searchTerm YEAR')
            ->andWhere('caches.status <= 2')
            ->andWhere(('caches.user_id = user.user_id'))
            ->setParameters(['searchTerm' => 1])
            ->orderBy('user.last_login', 'ASC');

        return $this->render(
            'backend/support/databaseQueries.html.twig', [
                                                           'supportCachesForm' => $formSearch->createView(),
                                                           'suppSQLquery4' => $qb->execute()->fetchAll()
                                                       ]
        );
    }

    /**
     * @return Response
     * @throws RecordsNotFoundException
     * @Route("/dbQueries5", name="support_db_queries_5")
     */
    public function executeSQL_support_commented_user() // List users where a support user left a comment.
    : Response
    {
        $formSearch = $this->createForm(SupportSearchCaches::class);

        return $this->render(
            'backend/support/databaseQueries.html.twig', [
                                                           'supportCachesForm' => $formSearch->createView(),
                                                           'suppSQLquery5' => $this->supportUserCommentsRepository->fetchAll()
                                                       ]
        );
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

    /**
     * @param Request $request
     * @param int $userID
     *
     * @return Response
     * @throws DBALException
     * @throws RecordNotFoundException
     * @throws RecordNotPersistedException
     *
     * @route("/supportUADactions/{userID}", name="support_executeUAD_actions")
     */
    public function executeUAD_actions(Request $request, int $userID)
    : Response {
        $form = $this->createForm(SupportUserAccountDetails::class)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->getClickedButton() === $form->get('button_account_inactive')) {
                // TODO: fill
            } elseif ($form->getClickedButton() === $form->get('button_GDPR_deletion')) {
                // TODO: fill
            } elseif ($form->getClickedButton() === $form->get('button_mark_email_invalid')) {
                $entity = $this->userRepository->fetchOneById($userID);
                $entity->emailProblems = 1;
                $this->userRepository->update($entity);
            } else {
                print("upsi?");
                die();
            }
        }

        return $this->redirectToRoute('backend_support_user_account_details', ['userID' => $userID]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @throws \Exception
     *
     * @route("/GPXimport/", name="support_gpx_import"), methods={"POST"}
     */
    public function GPX_import(Request $request)
    : Response {
        // Button/Dialog zum Einlesen der GPX-Datei
        // inklusive Rückinfo zu Anzahl eingelesener Caches und Anzahl zuordnbarer OC-Caches

        $formSearch = $this->createForm(SupportSearchCaches::class);
        $formUpload = $this->createForm(SupportImportGPX::class);
        $amountImportedCaches = 0;
        $amountAssignedCaches = 0;

        $formUpload->handleRequest($request);

        //        if ($formUpload->isSubmitted() && $formUpload->isValid()) {
        if ($formUpload->isSubmitted()) {
            $strData = file_get_contents($formUpload['gpx_file']->getData()->getRealPath());
            $strData = str_replace(['<groundspeak:', '</groundspeak:'], ['<', '</'], $strData);

            if ($strData === false || $strData == '') {
                // TODO: füllen?
            } else {
                // TODO: die ganze(n) GPX+XML Import/Export-Funktionen in ein eigenes Repository auslagern. Sofern es nix Controllerpszifisches ist
                $objXmlDocument = simplexml_load_string($strData);

                $objJsonDocument = json_encode($objXmlDocument);
                $arrOutput = json_decode($objJsonDocument, true);

                // Array auseinandernehmen und in ein datenbankfähiges Format umwandeln (für Tabelle support_listing_infos)
                $waypoints_as_array = [];

                foreach ($arrOutput['wpt'] as $wpt) {
                    $wpt_array['wp_oc'] = - 1;
                    $wpt_array['node_id'] = $this->nodesRepository->get_id_by_prefix(substr($wpt['name'], 0, 2));;
                    $wpt_array['node_owner_id'] =
                        0; // TODO: node_owner_id ist in GPX nicht enthalten. Aber eventuell in anderen Quellen? Zusätzlich node_owner_name etablieren
                    $wpt_array['node_owner_name'] = $wpt['cache']['owner']; // TODO: Feld in DB einfügen. #Migrations
                    $wpt_array['node_listing_id'] = substr($wpt['url'], strlen($wpt['url']) - 36, 36);
                    $wpt_array['node_listing_wp'] = $wpt['name'];
                    $wpt_array['node_listing_name'] = $wpt['cache']['name'];
                    $wpt_array['node_listing_size'] = 0; // $wpt['cache']['container']; // TODO: Tabellenspalte ändern in string statt int
                    $wpt_array['node_listing_difficulty'] = $wpt['cache']['difficulty'] * 2;
                    $wpt_array['node_listing_terrain'] = $wpt['cache']['terrain'] * 2;
                    $wpt_array['node_listing_coordinates_lat'] = $wpt['@attributes']['lat'];
                    $wpt_array['node_listing_coordinates_lon'] = $wpt['@attributes']['lon'];
                    $wpt_array['node_listing_available'] = ($wpt['cache']['@attributes']['available'] === "True") ? 1 : 0;
                    $wpt_array['node_listing_archived'] = ($wpt['cache']['@attributes']['archived'] === "True") ? 1 : 0;
                    $wpt_array['importstatus'] = 0;

                    array_push($waypoints_as_array, $wpt_array);
                    $amountImportedCaches ++;
                }

                // die eingelesenen Daten in die Datenbank übernehmen
                // TODO: ggf. gleich mit in die obige Schleife einbauen.. ist dann ein Aufwasch..
                foreach ($waypoints_as_array as $wpt) {
                    $entity = $this->supportListingInfosRepository->getEntityFromDatabaseArray($wpt);
                    $this->supportListingInfosRepository->create($entity);
                }

                // Die importieren Datensätze durchgehen, ob sie einem OC-Cache zugewiesen werden können
                // Im Fokus stehen alle DB-Einträge, bei denen gilt: support_listing_infos/wp_oc=-1
                // TODO: ggf. gleich mit in die obige Schleife einbauen? Oder lieber gleich die ganze DB-Tabelle anschauen, damit auch andere Einträge (nochmal) geprüft werden..
                // Nach Abschluss der Prüfung den import_status entsprechend setzen (5 bzw. 10)
                //  0 - frisch importiert
                //  5 - OC-GC-Überprüfung beendet, keine automatische Zuordnung möglich. Eine manuelle Zuweisung muss erfolgen.
                // 10 - OC-GC-Überprüfung beendet, automatische Zuordnung ist erfolgt.
                // 15 - Änderungen gegenüber einem früheren Import wurden erkannt und müssen noch verarbeitet werden
                // 20 - Import abgeschlossen. Änderungen, sofern vorhanden, wurden verarbeitet.
                //
                $fetchedCaches = $this->supportListingInfosRepository->fetchBy(['wp_oc' => - 1]);

                foreach ($fetchedCaches as $fetchedCache) {
                    $fetchedOcCaches = $this->cachesRepository->fetchBy(['wp_gc' => $fetchedCache->nodeListingWP]);

                    if (count($fetchedOcCaches) == 1) {
                        // wp_oc und import_status setzen

                    } else {
                        // Entweder gar keiner oder mehr als 1 möglicher OC-Cache in caches/wp_gc gefunden.. automatische Zurodnung nicht nöglich
                        // import_status setzen

                        // caches/wp_gc_maintained noch prüfen.. gleiches Spiel nochmal..
                    }
                }
            }
        } else {
            // TODO: füllen?
        }

        return $this->render(
            'backend/support/occ_gpx_import.html.twig', [
                                                          'supportCachesForm' => $formSearch->createView(),
                                                          'supportUploadGPXForm' => $formUpload->createView(),
                                                          'amountImportedCaches' => $amountImportedCaches,
                                                          'amountAssignedCaches' => $amountAssignedCaches
                                                      ]
        );
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @route("/GPXassignImports/", name="support_gpx_assign_imports")
     */
    public function GPX_assign_imports(Request $request)
    : Response {
        // Die beim Import nicht eindeutig als OC/OCOnly zuordenbaren GC-Caches müssen manuell begutachtet werden.
        // Optionen:
        //      als GC-Only markieren
        //      für den Moment ignorieren
        //      Eingabefeld für OC-Code anbieten, dessen Inhalt in der DB abgelegt wird
        //      (Tabellen caches/wp_gc_maintained, support_listing_infos/wp_oc)

        $formSearch = $this->createForm(SupportSearchCaches::class);

        $rueckinfo = '';

        return $this->render(
            'backend/support/occ_gpx_import.html.twig', ['supportCachesForm' => $formSearch->createView(), 'suppSQLquery5' => $rueckinfo]
        );
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @route("/GPXcheckDifferences/", name="support_gpx_check_differences")
     */
    public function GPX_check_for_differences(Request $request)
    : Response {
        // Überprüfung der Tabelle support_listing_infos
        // Erkennung von Veränderungen (Name, Wertung,..) einzelner OC/OCOnly Caches
        // Vermerken dieser Änderungen in der Tabelle support_listing_comments
        // Entfernung von Doubletten und Alteinträgen aus der Tabelle support_listing_infos

        $formSearch = $this->createForm(SupportSearchCaches::class);

        $rueckinfo = '';

        return $this->render(
            'backend/support/occ_gpx_import.html.twig', ['supportCachesForm' => $formSearch->createView(), 'suppSQLquery5' => $rueckinfo]
        );
    }
}
