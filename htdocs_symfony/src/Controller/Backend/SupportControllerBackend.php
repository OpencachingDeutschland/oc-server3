<?php

declare(strict_types=1);

namespace Oc\Controller\Backend;

use DateTime;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\InvalidArgumentException;
use Exception;
use Oc\Entity\SupportListingCommentsEntity;
use Oc\Entity\SupportUserCommentsEntity;
use Oc\Entity\UserLoginBlockEntity;
use Oc\Form\SupportBonusCachesAssignment;
use Oc\Form\SupportCommentField;
use Oc\Form\SupportImportGPX;
use Oc\Form\SupportRestoreCache;
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
use Oc\Repository\CoordinatesRepository;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;
use Oc\Repository\NodesRepository;
use Oc\Repository\SecurityRolesRepository;
use Oc\Repository\SupportBonuscachesRepository;
use Oc\Repository\SupportListingCommentsRepository;
use Oc\Repository\SupportListingInfosRepository;
use Oc\Repository\SupportUserCommentsRepository;
use Oc\Repository\SupportUserRelationsRepository;
use Oc\Repository\UserLoginBlockRepository;
use Oc\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Security("is_granted('ROLE_SUPPORT_TRAINEE')") // now, the whole Support functions are limited to ROLE_.. and above!
 */
class SupportControllerBackend extends AbstractController
{
    //  0 - frisch importiert
    // 20 - Import abgeschlossen. Änderungen, sofern vorhanden, wurden verarbeitet.
    private const IMPORT_STATUS_NEW = 0;

    private const IMPORT_STATUS_FINISHED = 20;

    private Connection $connection;

    private CacheAdoptionsRepository $cacheAdoptionsRepository;

    private CacheCoordinatesRepository $cacheCoordinatesRepository;

    private CacheLogsArchivedRepository $cacheLogsArchivedRepository;

    private CacheReportsRepository $cacheReportsRepository;

    private CachesRepository $cachesRepository;

    private CacheStatusModifiedRepository $cacheStatusModifiedRepository;

    private CacheStatusRepository $cacheStatusRepository;

    private NodesRepository $nodesRepository;

    private SupportBonuscachesRepository $supportBonuscachesRepository;

    private SupportListingCommentsRepository $supportListingCommentsRepository;

    private SupportListingInfosRepository $supportListingInfosRepository;

    private SupportUserCommentsRepository $supportUserCommentsRepository;

    private SupportUserRelationsRepository $supportUserRelationsRepository;

    private UserLoginBlockRepository $userLoginBlockRepository;

    private UserRepository $userRepository;

    public function __construct(
            Connection $connection,
            CacheAdoptionsRepository $cacheAdoptionsRepository,
            CacheCoordinatesRepository $cacheCoordinatesRepository,
            CacheLogsArchivedRepository $cacheLogsArchivedRepository,
            CacheReportsRepository $cacheReportsRepository,
            CachesRepository $cachesRepository,
            CacheStatusModifiedRepository $cacheStatusModifiedRepository,
            CacheStatusRepository $cacheStatusRepository,
            NodesRepository $nodesRepository,
            SecurityRolesRepository $securityRolesRepository,
            SupportBonuscachesRepository $supportBonuscachesRepository,
            SupportListingCommentsRepository $supportListingCommentsRepository,
            SupportListingInfosRepository $supportListingInfosRepository,
            SupportUserCommentsRepository $supportUserCommentsRepository,
            SupportUserRelationsRepository $supportUserRelationsRepository,
            UserLoginBlockRepository $userLoginBlockRepository,
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
        $this->securityRolesRepository = $securityRolesRepository;
        $this->supportBonuscachesRepository = $supportBonuscachesRepository;
        $this->supportListingCommentsRepository = $supportListingCommentsRepository;
        $this->supportListingInfosRepository = $supportListingInfosRepository;
        $this->supportUserCommentsRepository = $supportUserCommentsRepository;
        $this->supportUserRelationsRepository = $supportUserRelationsRepository;
        $this->userLoginBlockRepository = $userLoginBlockRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/support", name="support_index")
     */
    public function index(): Response
    {
        return $this->render('backend/support/index.html.twig');
    }

    /**
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws Exception
     * @Route("/supportSearch", name="support_search")
     */
    public function searchCachesAndUser(Request $request): Response
    {
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
     * @throws RecordNotFoundException
     * @throws RecordsNotFoundException
     * @throws Exception
     * @Route("/reportedCaches", name="support_reported_caches")
     * @Security("is_granted('ROLE_SUPPORT_TRAINEE')")
     */
    public function listReportedCaches(): Response
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
     * @Route("/bonusCaches", name="support_bonus_caches")
     * @Security("is_granted('ROLE_SUPPORT_MAINTAIN')")
     */
    public function listBonusCaches(): Response
    {
        $formSearch = $this->createForm(SupportSearchCaches::class);
        $formAssignBonusCache = $this->createForm(SupportBonusCachesAssignment::class);

        $fetchedBonuscaches = $this->getBonusCaches();

        return $this->render(
                'backend/support/bonusCaches.html.twig', [
                        'supportCachesForm' => $formSearch->createView(),
                        'supportAssignBonusCacheForm' => $formAssignBonusCache->createView(),
                        'bonusCaches_by_id' => $fetchedBonuscaches
                ]
        );
    }

    /**
     * @throws Exception
     * @throws RecordNotFoundException
     * @Route("/bonusCachesAssignmentChoice/{wpID}", name="support_bonus_caches_assignment_choice")
     * @Security("is_granted('ROLE_SUPPORT_MAINTAIN')")
     */
    public function bonusCachesAssignmentChoice(string $wpID): Response
    {
        $formSearch = $this->createForm(SupportSearchCaches::class);

        $fetchedCache = $this->cachesRepository->fetchOneBy(['wp_Oc' => $wpID]);
        $fetchedOwnerCaches = $this->cachesRepository->fetchBy(['user_id' => $fetchedCache->userId]);

        return $this->render(
                'backend/support/bonusCachesAssignment.html.twig', [
                        'supportCachesForm' => $formSearch->createView(),
                        'bonus_Cache' => $wpID,
                        'caches_by_owner' => $fetchedOwnerCaches
                ]
        );
    }

    /**
     * @throws Exception
     * @throws RecordAlreadyExistsException
     * @throws RecordNotFoundException
     * @throws RecordNotPersistedException
     * @Route("/bonusCachesAssignment/{wpID}&{userID}&{toBonusCache}", name="support_bonus_caches_assignment")
     * @Security("is_granted('ROLE_SUPPORT_MAINTAIN')")
     */
    public function bonusCachesAssignment(string $wpID, int $userID, string $toBonusCache): Response
    {
        $formSearch = $this->createForm(SupportSearchCaches::class);

        $fetchedOwnerCaches = $this->cachesRepository->fetchBy(['user_id' => $userID]);

        $this->supportBonuscachesRepository->update_or_create_bonus_entry($wpID, $toBonusCache);

        return $this->render(
                'backend/support/bonusCachesAssignment.html.twig', [
                        'supportCachesForm' => $formSearch->createView(),
                        'bonus_Cache' => $toBonusCache,
                        'caches_by_owner' => $fetchedOwnerCaches
                ]
        );
    }

    /**
     * @throws Exception
     * @throws RecordAlreadyExistsException
     * @throws RecordNotFoundException
     * @throws RecordNotPersistedException
     * @Route("/bonusCachesDirectAssignment", name="support_directly_assign_bonus_cache")
     * @Security("is_granted('ROLE_SUPPORT_MAINTAIN')")
     */
    public function bonusCachesDirectAssignment(Request $request): Response
    {
        $formDirectBonusAssignment = $this->createForm(SupportBonusCachesAssignment::class);

        $formDirectBonusAssignment->handleRequest($request);
        if ($formDirectBonusAssignment->isSubmitted() && $formDirectBonusAssignment->isValid()) {
            $inputData = $formDirectBonusAssignment->getData();
            $cacheBelongToBonus = strtoupper($inputData['content_wp_to_be_assigned'] . '');
            $cacheIsBonus = strtoupper($inputData['content_wp_that_is_bonus_cache']);

            if (!($this->cachesRepository->isNew($cacheIsBonus)) && ($cacheBelongToBonus === '')) {
                $this->supportBonuscachesRepository->update_or_create_bonus_entry($cacheIsBonus, '', true);
            } elseif (!($this->cachesRepository->isNew($cacheBelongToBonus)) && !($this->cachesRepository->isNew($cacheIsBonus))) {
                $this->supportBonuscachesRepository->update_or_create_bonus_entry($cacheBelongToBonus, $cacheIsBonus);
            }
        }

        return $this->redirectToRoute('backend_support_bonus_caches');
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws RecordNotFoundException
     * @throws RecordNotPersistedException
     * @Route("/removeBonusCachesAssignment/{wpID}&{removeToBonus}&{removeBonus}", name="support_remove_bonus_caches_assignment")
     * @Security("is_granted('ROLE_SUPPORT_MAINTAIN')")
     */
    public function removeBonusCachesAssignment(string $wpID, bool $removeToBonus, bool $removeBonus): Response
    {
        $fetchedBonusCache = $this->supportBonuscachesRepository->fetchOneBy(['wp_oc' => $wpID]);

        if ($fetchedBonusCache) {
            if ($removeToBonus) {
                $fetchedBonusCache->belongsToBonusCache = '';
            }
            if ($removeBonus) {
                $fetchedBonusCache->isBonusCache = false;
            }
        }

        if (($fetchedBonusCache->belongsToBonusCache == '') && !$fetchedBonusCache->isBonusCache) {
            $this->supportBonuscachesRepository->remove($fetchedBonusCache);
        } else {
            $this->supportBonuscachesRepository->update($fetchedBonusCache);
        }

        return $this->redirectToRoute('backend_support_bonus_caches');
    }

    /**
     * @throws Exception
     * @Route("/dbQueries", name="support_db_queries")
     */
    public function listDbQueries(Request $request): Response
    {
        $fetchedInformation = [];

        $formSearch = $this->createForm(SupportSearchCaches::class);
        $formSQLFlex = $this->createForm(SupportSQLFlexForm::class);

        $formSQLFlex->handleRequest($request);

        if ($formSQLFlex->isSubmitted() && $formSQLFlex->isValid()) {
            $inputData = $formSQLFlex->getData();

            $fetchedInformation =
                    $this->executeSQL_flexible($inputData['content_WHAT'], $inputData['content_TABLE'], (string)$inputData['content_CONDITION']);

            $countFetched = count($fetchedInformation);
            for ($i = 0; $i < $countFetched; $i++) {
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
                        'SQLFlexForm' => $formSQLFlex->createView(),
                        'suppSQLqueryFlex' => $fetchedInformation
                ]
        );
    }

    /**
     * @throws RecordNotFoundException
     * @throws RecordsNotFoundException
     * @throws Exception
     * @throws Exception
     * @Route("/cacheHistory/{wpID}", name="support_cache_history")
     * @Security("is_granted('ROLE_SUPPORT_TRAINEE')")
     */
    public function list_cache_history(string $wpID): Response
    {
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
     * @throws RecordNotFoundException
     * @throws RecordsNotFoundException
     * @throws Exception
     * @Route("/repCaches/{repID}", name="support_reported_cache")
     * @Security("is_granted('ROLE_SUPPORT_TRAINEE')")
     */
    public function list_reported_cache_details(int $repID): Response
    {
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
     * @throws Exception
     * @throws RecordAlreadyExistsException
     * @throws RecordNotFoundException
     * @Route("/occ/{wpID}&{userID}", name="support_occ")
     * @Security("is_granted('ROLE_SUPPORT_MAINTAIN')")
     */
    public function occPage(string $wpID, int $userID): Response
    {
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
            } catch (Exception $exception) {
                $entity = new SupportListingCommentsEntity($wpID);
                $fetchedCacheComments = $this->supportListingCommentsRepository->create($entity);
            }
            // Cachedaten zu Fremnodes abholen (es können mehrere Einträge in der DB existieren)
            try {
                $fetchedCacheInfos = $this->supportListingInfosRepository->fetchBy(['wp_oc' => $wpID]);
            } catch (Exception $exception) {
            }
        }

        // Basisnutzerdaten abolen
        $fetchedUserData = $this->userRepository->fetchOneById($userID);
        // Supportkommentar zum Nutzer abolen. Ggf. neuen, leeren anlegen.
        try {
            $fetchedUserComments = $this->supportUserCommentsRepository->fetchOneBy(['oc_user_id' => $userID]);
        } catch (Exception $exception) {
            $entity = new SupportUserCommentsEntity($userID);
            $fetchedUserComments = $this->supportUserCommentsRepository->create($entity);
        }
        // Nutzerdaten zu Fremnodes abholen (es können mehrere Einträge in der DB existieren)
        try {
            $fetchedUserRelations = $this->supportUserRelationsRepository->fetchBy(['oc_user_id' => $userID]);
        } catch (Exception $exception) {
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
     * @throws RecordNotFoundException
     * @throws RecordNotPersistedException
     * @throws Exception
     * @Route("/occSaveText", name="support_occ_save_text")
     * @Security("is_granted('ROLE_SUPPORT_MAINTAIN')")
     */
    public function occ_saveTextArea(Request $request): Response
    {
        $form = $this->createForm(SupportCommentField::class)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $inputData = $form->getData();

            if ($inputData['hidden_sender'] == 'textfield_cache_comment') {
                $entity = $this->supportListingCommentsRepository->fetchOneBy(['wp_oc' => (string)$inputData['hidden_ID2']]);
                $entity->comment = $inputData['content_comment_field'];
                $this->supportListingCommentsRepository->update($entity);
            } elseif ($inputData['hidden_sender'] == 'textfield_user_comment') {
                $entity = $this->supportUserCommentsRepository->fetchOneBy(['oc_user_id' => (int)$inputData['hidden_ID1']]);
                $entity->comment = $inputData['content_comment_field'];
                $this->supportUserCommentsRepository->update($entity);
            }

            return $this->redirectToRoute('backend_support_occ', [
                    'userID' => (string)$inputData['hidden_ID1'],
                    'wpID' => (string)$inputData['hidden_ID2']
            ]);
        }

        return $this->redirectToRoute('backend_support_occ');
    }

    /**
     * @throws RecordNotFoundException
     * @throws RecordNotPersistedException
     * @throws Exception
     * @Route("/repCachesSaveText", name="support_reported_cache_save_text")
     * @Security("is_granted('ROLE_SUPPORT_TRAINEE')")
     */
    public function repCaches_saveTextArea(Request $request): Response
    {
        $form = $this->createForm(SupportCommentField::class)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $inputData = $form->getData();

            $entity = $this->cacheReportsRepository->fetchOneBy(['id' => (int)$inputData['hidden_ID1']]);
            $entity->comment = $inputData['content_comment_field'];

            $this->cacheReportsRepository->update($entity);

            return $this->redirectToRoute('backend_support_reported_cache', ['repID' => $entity->id]);
        }

        return $this->redirectToRoute('backend_support_reported_caches');
    }

    /**
     * @throws RecordNotFoundException
     * @throws RecordNotPersistedException
     * @throws Exception
     * @route("/repCachesAssignSupportuser/{repID}&{adminId}&{route}", name="support_reported_cache_supportuser_assignment")
     * @Security("is_granted('ROLE_SUPPORT_TRAINEE')")
     */
    public function repCaches_supportuser_assignment(int $repID, int $adminId, string $route): Response
    {
        $entity = $this->cacheReportsRepository->fetchOneBy(['id' => $repID]);
        $entity->adminid = $adminId;

        $this->cacheReportsRepository->update($entity);

        return $this->redirectToRoute($route, ['repID' => $repID]);
    }

    /**
     * @throws RecordNotFoundException
     * @throws RecordNotPersistedException
     * @throws Exception
     * @route("/repCachesAssignSupportuser/{repID}&{route}", name="support_reported_cache_set_status")
     * @Security("is_granted('ROLE_SUPPORT_TRAINEE')")
     */
    public function repCaches_setReportStatus(int $repID, string $route): Response
    {
        $entity = $this->cacheReportsRepository->fetchOneBy(['id' => $repID]);
        $entity->status = 3; // ToDo: die '3' hart vorgeben? Oder wie?

        $this->cacheReportsRepository->update($entity);

        return $this->redirectToRoute($route, ['repID' => $repID]);
    }

    /**
     * @throws Exception
     * @throws RecordNotFoundException
     * @Route("/uad/{userID}", name="support_user_account_details")
     * @Security("is_granted('ROLE_SUPPORT_TRAINEE')")
     */
    public function list_user_account_details(int $userID): Response
    {
        $fetchedUserDetails = $this->userRepository->fetchOneById($userID);
        $fetchedUserLoginBlock = null;
        try {
            $fetchedUserLoginBlock = $this->userLoginBlockRepository->fetchOneBy(['user_id' => $userID]);
        } catch (RecordNotFoundException $e) {
        }

        return $this->render(
                'backend/support/userDetails.html.twig', [
                        'supportCachesForm' => $this->createForm(SupportSearchCaches::class)->createView(),
                        'supportUserAccountActions' => $this->createForm(SupportUserAccountDetails::class)->createView(),
                        'user_account_details' => $fetchedUserDetails,
                        'user_login_block' => $fetchedUserLoginBlock
                ]
        );
    }

    /**
     * @throws Exception
     * @throws RecordNotFoundException
     *
     * @Route("/vandalism/{wpID}&{userID}", name="support_vandalism")
     * @Security("is_granted('ROLE_SUPPORT_MAINTAIN')")
     */
    public function vandalism(string $wpID, int $userID): Response
    {
        $data = $this->get_archive_data($this->cachesRepository->getIdByWP($wpID), $wpID);

        dd($data);

        return $this->render(
                'backend/support/vandalism.html.twig', [
                        'supportRestoreCacheForm' => $this->createForm(SupportRestoreCache::class)->createView(),
                        'curremt_cache_details' => $this->cachesRepository->fetchOneBy(['wp_Oc' => $wpID]),
                        'user_account_details' => $this->userRepository->fetchOneById($userID),
                        'modified_information' => $data
                ]
        );
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    function get_archive_data(int $cacheId, string $wpID): array
    {
        $data = [];
        $admins = [];

        // make waypoint index
        $wp_oc[$cacheId] = $wpID;

        // process cache coordinates
        $rs = $this->connection->createQueryBuilder()
                ->select('cache_id', 'LEFT(date_created, 10) AS date_modified', 'longitude', 'latitude', 'restored_by')
                ->from('cache_coordinates')
                ->where('cache_id = :paramID')
                ->setparameters(['paramID' => $cacheId])
                ->orderby('date_created', 'ASC')
                ->executeQuery()
                ->fetchAllAssociative();
        // order is relevant, because multiple changes per day possible
        $lastcoord = [];
        foreach ($rs as $r) {
            $coord = new CoordinatesRepository((float)$r['latitude'], (float)$r['longitude']);
            $coord = $coord->getDegreeMinutes();
            $coord = $coord['lat'] . " " . $coord['lon'];
            if (isset($lastcoord[$r['cache_id']]) && $coord != $lastcoord[$r['cache_id']]) {
                $this->append_data($data, $admins, $wp_oc, $r, "coord", $lastcoord[$r['cache_id']], $coord);
            }
            $lastcoord[$r['cache_id']] = $coord;
        }

        // process cache country
        $rs = $this->connection->createQueryBuilder()
                ->select('cache_id', 'LEFT(date_created, 10) AS date_modified', 'country', 'restored_by')
                ->from('cache_countries')
                ->where('cache_id = :paramID')
                ->setparameters(['paramID' => $cacheId])
                ->orderby('date_created', 'ASC')
                ->executeQuery()
                ->fetchAllAssociative();
        // order is relevant, because multiple changes per day possible
        $lastcountry = [];
        foreach ($rs as $r) {
            if (isset($lastcountry[$r['cache_id']]) && $r['country'] != $lastcountry[$r['cache_id']]) {
                $this->append_data($data, $admins, $wp_oc, $r, "country", $lastcountry[$r['cache_id']], $r['country']);
            }
            $lastcountry[$r['cache_id']] = $r['country'];
        }

        // process all other cache data
        // first the current data ...
        $nextcd = [];
        $rs = $this->connection->createQueryBuilder()
                ->select('*')
                ->from('caches')
                ->where('cache_id = :paramID')
                ->setParameters(['paramID' => $cacheId])
                ->executeQuery()
                ->fetchAllAssociative();
        foreach ($rs as $r) {
            $nextcd[$r['wp_oc']] = $r;
            $user_id = $r['user_id']; // is used later for logs
        }

        // .. then the changes
        // .. and then the changes
        $rs = $this->connection->createQueryBuilder()
                ->select('*')
                ->from('caches_modified')
                ->where('cache_id = :paramID')
                ->setParameters(['paramID' => $cacheId])
                ->orderBy('date_modified', 'DESC')
                ->executeQuery()
                ->fetchAllAssociative();
        foreach ($rs as $r) {
            $wp = $wp_oc[$r['cache_id']];
            if ($r['name'] != $nextcd[$wp]['name']) {
                append_data($data, $admins, $wp_oc, $r, 'name', $r['name'], $nextcd[$wp]['name']);
            }

            if ($r['type'] != $nextcd[$wp]['type']) {
                $this->append_data(
                        $data,
                        $admins,
                        $wp_oc,
                        $r,
                        'type',
                        $this->connection->createQueryBuilder()
                                ->select('name')
                                ->from('cache_type')
                                ->where('id = :paramID')
                                ->setParameters(['paramID' =>$r['type']])
                                ->executeQuery()
                                ->fetchAssociative()['name'],
                        $this->connection->createQueryBuilder()
                                ->select('name')
                                ->from('cache_type')
                                ->where('id = :paramID')
                                ->setParameters(['paramID' =>$nextcd[$wp]['type']])
                                ->executeQuery()
                                ->fetchAssociative()['name']
                );

                size..
            }
            dd($data);

        }

////////////while ($r = sql_fetch_assoc($rs)) {
//            if ($r['size'] != $nextcd[$wp]['size']) {
//                append_data(
//                        $data,
//                        $admins,
//                        $wp_oc,
//                        $r,
//                        "size",
//                        labels::getLabelValue('cache_size', $r['size']),
//                        labels::getLabelValue('cache_size', $nextcd[$wp]['size'])
//                );
//            }
//            if ($r['difficulty'] != $nextcd[$wp]['difficulty']) {
//                append_data($data, $admins, $wp_oc, $r, "D", $r['difficulty'] / 2, $nextcd[$wp]['difficulty'] / 2);
//            }
//            if ($r['terrain'] != $nextcd[$wp]['terrain']) {
//                append_data($data, $admins, $wp_oc, $r, "T", $r['terrain'] / 2, $nextcd[$wp]['terrain'] / 2);
//            }
//            if ($r['search_time'] != $nextcd[$wp]['search_time']) {
//                append_data(
//                        $data,
//                        $admins,
//                        $wp_oc,
//                        $r,
//                        'time',
//                        $r['search_time'] . '&nbsp;h',
//                        $nextcd[$wp]['search_time'] . '&nbsp;h'
//                );
//            }
//            if ($r['way_length'] != $nextcd[$wp]['way_length']) {
//                append_data(
//                        $data,
//                        $admins,
//                        $wp_oc,
//                        $r,
//                        'way',
//                        $r['way_length'] . '&nbsp;km',
//                        $nextcd[$wp]['way_length'] . '&nbsp;km'
//                );
//            }
//            if ($r['wp_gc'] != $nextcd[$wp]['wp_gc']) {
//                append_data(
//                        $data,
//                        $admins,
//                        $wp_oc,
//                        $r,
//                        'GC ',
//                        format_wp($r['wp_gc']),
//                        format_wp($nextcd[$wp]['wp_gc'])
//                );
//            }
//            if ($r['wp_nc'] != $nextcd[$wp]['wp_nc']) {
//                append_data(
//                        $data,
//                        $admins,
//                        $wp_oc,
//                        $r,
//                        'GC ',
//                        format_wp($r['wp_nc']),
//                        format_wp($nextcd[$wp]['wp_nc'])
//                );
//            }
//            if ($r['date_hidden'] != $nextcd[$wp]['date_hidden']) {
//                append_data($data, $admins, $wp_oc, $r, "hidden", $r['date_hidden'], $nextcd[$wp]['date_hidden']);
//            }
//
//            $nextcd[$wp] = $r;
//        }


        // done
        ksort($data);

        return array_reverse($data, true);
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    function append_data(&$data, &$admins, $wp_oc, $r, $field, $oldvalue, $newvalue)
    {
        if (!isset($r['date_modified'])) {
            die('internal error: date_modified not set for: ' . $field);
        }
        $mdate = $r['date_modified'];
        $wp = $wp_oc[$r['cache_id']];
        $byadmin = ($r['restored_by'] > 0);

        if (!isset($data[$mdate])) {
            $data[$mdate] = [];
        }

        // TODO: HTML+CSS anpassen, da das vom Legacycode herauskopiert wurde und nun nicht mehr so recht passt
        $text = '<strong';
        if ($byadmin) {
            $text .= " class='adminrestore'";
        } else {
            $text .= " class='userchange'";
        }
        $text .= ">$field</strong>: $oldvalue" . ($newvalue != '' ? " &rarr; $newvalue" : '');
        if (isset($data[$mdate][$wp])) {
            $data[$mdate][$wp] .= ', ' . $text;
        } else {
            $data[$mdate][$wp] = $text;
        }

        if ($byadmin) {
            if (!isset($admins[$mdate])) {
                $admins[$mdate] = [];
            }
            if (!isset($admins[$mdate][$wp])) {
                $admins[$mdate][$wp] = [];
            }
            // TODO: Verlinkung anpassen.. das ist die alte Legacy-Verlinkungsvariante
            $admins[$mdate][$wp][$r['restored_by'] + 0]
                    = "<a href='viewprofile.php?userid=" . $r['restored_by'] . "' target='_blank'>" .
                    $this->connection->createQueryBuilder()
                            ->select('username')
                            ->from('user')
                            ->where('user_id = :paramUser')
                            ->setParameters(['paramUser' => $r['restored_by']])
                            ->executeQuery()
                            ->fetchAssociative()['username']
                    . '</a>';
        }
    }

    /**
     * @throws Exception
     *
     * @Route("/vandalismRestore", name="support_vandalism_restore")
     * @Security("is_granted('ROLE_SUPPORT_MAINTAIN')")
     */
    public function vandalismRestore(Request $request): Response
    {
        // get current cache data
        $restoreDate = $request->get('dateselect'); // $rdate
        $wpID = $request->get('wpID');
        $cacheID = $this->cachesRepository->getIdByWP($wpID);
        $adminID = $request->get('adminUserID');
        $cache = $this->cachesRepository->fetchOneBy(['wp_Oc' => $wpID]);
        $userId = $cache->userId;

        $restored = [];
        $modified = false;

        // coordinates
        if ($request->get('restore_coords') &&
                null !== $this->connection->createQueryBuilder()
                        ->select('cache_id')
                        ->from('cache_coordinates')
                        ->where('cache_id = :paramID')
                        ->setParameters(['paramID' => $cacheID])
                        ->executeQuery()
                        ->fetchAssociative()
        ) {
            $rs = $this->connection->createQueryBuilder()
                    ->select('latitude', 'longitude')
                    ->from('cache_coordinates')
                    ->where('cache_id = :paramID')
                    ->andWhere('date_created < :paramDate')
                    ->setParameters(['paramID' => $cacheID, 'paramDate' => $restoreDate])
                    ->orderBy('date_created', 'DESC')
                    ->executeQuery()
                    ->fetchAssociative();

            if (null !== $rs) {
//                 $yyy=$this->connection->createQueryBuilder()->expr()setValue("SET @restoredby='$adminID'")->getSQL(); // is evaluated by trigger functions
//                dd($yyy);
//                $rs = $this->connection->createQueryBuilder()->set('@restoredby', $adminID); // ->executeQuery() ?
                $xxx = $this->connection->createQueryBuilder()
                        ->update('caches')
//                        ->set('@restoredby', ':paramLat')
//                        ->set('longitude', ':paramLon')
//                        ->set('restored_by', ':paramAdminID')
                        ->where('cache_id = :paramID')
                        ->setParameters(
                                ['paramLat' => $rs['latitude'], 'paramLon' => $rs['longitude'], 'paramID' => $cacheID, 'paramAdminID' => $adminID]
                        )
                        ->executeStatement();
                dd([$xxx, "xx"]);
                $restored[$wpID]['coords'] = true;
            }
        }

        dd($restored);

        $qb->set('@restoredby', 0);
        return $this->redirectToRoute(); // TODO
    }

    /**
     * @throws Exception
     */
    private function getCachesForSearchField(string $searchtext, bool $limit = false): array
    {
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

        return $qb->executeQuery()->fetchAllAssociative();
    }

    /**
     * @Security("is_granted('ROLE_SUPPORT_MAINTAIN')")
     */
    public function getBonusCaches(): array
    {
        try {
            return $this->supportBonuscachesRepository->fetchAll();
        } catch (Exception $exception) {
            return [];
        }
    }

    /**
     * @throws RecordNotFoundException
     * @throws RecordsNotFoundException
     * @throws Exception
     * @Security("is_granted('ROLE_SUPPORT_TRAINEE')")
     */
    public function getReportedCaches(): array
    {
        return $this->cacheReportsRepository->fetchAll();
    }

    /**
     * @throws Exception
     * @Route("/dbQueries1/{days}", name="support_db_queries_1")
     * @Security("is_granted('ROLE_SUPPORT_TRAINEE')")
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
                        'suppSQLquery1' => $qb->executeQuery()->fetchAllAssociative()
                ]
        );
    }

    /**
     * @throws Exception
     * @Route("/dbQueries2/{days}", name="support_db_queries_2")
     * @Security("is_granted('ROLE_SUPPORT_TRAINEE')")
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
                        'suppSQLquery2' => $qb->executeQuery()->fetchAllAssociative()
                ]
        );
    }

    /**
     * @throws Exception
     * @Route("/dbQueries4", name="support_db_queries_4")
     * @Security("is_granted('ROLE_SUPPORT_TRAINEE')")
     */
    public function executeSQL_caches_old_login_date(
    ) // List (non-archived, non-locked) caches from users whose last login date is older than one year, and the caches have DNFs or notes.
    : Response
    {
        $formSearch = $this->createForm(SupportSearchCaches::class);

        $qb = $this->connection->createQueryBuilder();
        $qb->select(
                'caches.name',
                'caches.cache_id',
                'caches.status',
                'user.user_id',
                'user.username',
                'user.last_login',
                'count(cache_logs.type) as logCount'
        )
                ->distinct()
                ->from('caches')
                ->innerJoin('caches', 'user', 'user', 'caches.user_id = user.user_id')
                ->innerJoin('caches', 'cache_logs', 'cache_logs', 'caches.cache_id = cache_logs.cache_id')
                ->where('user.last_login < now() - interval :searchTerm YEAR')
                ->andWhere('caches.status <= 2')
                ->andWhere(('caches.user_id = user.user_id'))
                ->andWhere(('caches.cache_id = cache_logs.cache_id'))
                ->andWhere(('cache_logs.type = 2 or cache_logs.type = 3'))
                ->setParameters(['searchTerm' => 1])
                ->orderBy('user.last_login', 'ASC')
                ->groupBy('caches.name');

        return $this->render(
                'backend/support/databaseQueries.html.twig', [
                        'supportCachesForm' => $formSearch->createView(),
                        'suppSQLquery4' => $qb->executeQuery()->fetchAllAssociative()
                ]
        );
    }

    /**
     * @throws RecordsNotFoundException
     * @Route("/dbQueries5", name="support_db_queries_5")
     * @Security("is_granted('ROLE_SUPPORT_MAINTAIN')")
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
     * @throws Exception
     * @Route("/dbQueries6", name="support_db_queries_6")
     * @Security("is_granted('ROLE_SUPPORT_TRAINEE')")
     */
    public function executeSQL_Dornroeschen_caches() // List caches that currently meet Dornröschen requirements.
    : Response
    {
        $formSearch = $this->createForm(SupportSearchCaches::class);

        // Liste mit Caches erstellen, deren Listingeigenschaften auf Dornröschen zutreffen
        $qb_caches = $this->connection->createQueryBuilder();
        $qb_caches->select('caches.cache_id', 'caches.name', 'caches.wp_oc')
                ->from('caches')
                ->where('caches.size != 7')
                ->andWhere('caches.status <= 2')
                ->andWhere('caches.wp_gc = \'\'')
                ->andWhere('caches.wp_gc_maintained = \'\'')
                ->andWhere('caches.type IN (1, 2, 3, 7, 8, 9, 10)');
        $qb_caches_list = $qb_caches->executeQuery()->fetchAllAssociative();

        // Liste mit Fundlogs erstellen, die innerhalb der letzten zwei Jahre liegen
        $qb_logs = $this->connection->createQueryBuilder();
        $qb_logs->select('cache_logs.cache_id')
                ->from('cache_logs')
                ->where('cache_logs.type = 1', 'cache_logs.date > now() - INTERVAL 2 YEAR');
        $qb_logs_list = $qb_logs->executeQuery()->fetchAllAssociative();

        // Cacheliste reduzieren um die Caches, die innerhalb der letzten zwei Jahre einen Fund hatten
        foreach ($qb_logs_list as $qbll) {
            $found_key = array_search($qbll['cache_id'], array_column($qb_caches_list, 'cache_id'));

            if ($found_key) {
                unset($qb_caches_list[$found_key]);
            }
        }

        return $this->render(
                'backend/support/databaseQueries.html.twig', [
                        'supportCachesForm' => $formSearch->createView(),
                        'suppSQLquery6' => $qb_caches_list
                ]
        );
    }

    /**
     * @throws Exception
     * @Security("is_granted('ROLE_SUPPORT_MAINTAIN')")
     */
    public function executeSQL_flexible(string $what, string $table, string $condition): array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select($what)
                ->from($table);
        if ($condition != '') {
            $qb->where($condition);
        }

        return ($qb->executeQuery()->fetchAllAssociative());
    }

    /**
     * @throws RecordNotFoundException
     * @throws RecordNotPersistedException
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws Exception
     * @route("/supportUADactions/{userID}", name="support_executeUAD_actions")
     * @Security("is_granted('ROLE_SUPPORT_MAINTAIN')")
     */
    public function executeUAD_actions(Request $request, int $userID): Response
    {
        $form = $this->createForm(SupportUserAccountDetails::class)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->getClickedButton() === $form->get('button_account_inactive')) {
                // TODO: fill
            } elseif ($form->getClickedButton() === $form->get('button_login_block')) {
                $user = $this->userRepository->fetchOneById($userID);
                $userToBlockIsHigher = false;
                $userToBlockRoles = $user->getRoles();

                if ($userToBlockRoles) {
                    foreach ($userToBlockRoles as $role) {
                        if (!$this->isGranted($role)) {
                            $userToBlockIsHigher = true;
                        }
                    }
                }

                if ($this->isGranted('ROLE_SUPPORT_HEAD') && !$userToBlockIsHigher) {
                    $timeToBlock = $form->get('dropDown_login_block')->getData();
                    $message = $form->get('message_login_block')->getData();

                    try {
                        $entity = $this->userLoginBlockRepository->fetchOneBy(['user_id' => $userID]);

                        if ($timeToBlock === -1) {
                            // -1 = entferne die Blockierung des Logins // alle anderen Zahlen = setze die blockierung auf $JETZT plus x Tage
                            $this->userLoginBlockRepository->remove($entity);
                        } else {
                            // setze Zeitstemepel + x Tage
                            $untilWhenToBlock = new DateTime(date('Y-m-d H:i:s') . '+ ' . $timeToBlock . ' day');
                            $entity->loginBlockUntil = $untilWhenToBlock->format('Y-m-d H:i:s');
                            $entity->message = $message;
                            $this->userLoginBlockRepository->update($entity);
                        }
                    } catch (RecordNotFoundException $e) {
                        $untilWhenToBlock = new DateTime(date('Y-m-d H:i:s') . '+ ' . $timeToBlock . ' day');
                        $entity = new UserLoginBlockEntity($userID, $untilWhenToBlock->format('Y-m-d H:i:s'), $message);
                        $entity = $this->userLoginBlockRepository->create($entity);
                    }
                }
            } elseif ($form->getClickedButton() === $form->get('button_GDPR_deletion')) {
                // TODO: fill
                // Achtung: für die DSGVO-Löschung müssen vorher der Account und die dazugehörigen Cachelistings deaktiviert werden!
                //          Sonst kommt es zu unerwünschtem Verhalten (Caches noch logbar, obwohl Account deaktiviert ..)
                //          Da das dem Inhalt des ersten Buttons (button_account_inactive) entspricht, genügt es vermutlich, dessen Funktion aufzurufen
            } elseif ($form->getClickedButton() === $form->get('button_mark_email_invalid')) {
                $entity = $this->userRepository->fetchOneById($userID);
                $entity->emailProblems = true;
                $this->userRepository->update($entity);
            } else {
                print("upsi?");
                die();
            }
        }

        return $this->redirectToRoute('backend_support_user_account_details', ['userID' => $userID]);
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws RecordAlreadyExistsException
     * @throws RecordNotFoundException
     * @throws RecordNotPersistedException
     * @throws RecordsNotFoundException
     * @throws \Doctrine\DBAL\Driver\Exception
     * @route("/GPXimport/", name="support_gpx_import"), methods={"POST"}
     * @Security("is_granted('ROLE_SUPPORT_MAINTAIN')")
     *
     * Button/Dialog zum Einlesen der GPX-Datei
     * inklusive Rückinfo zu Anzahl eingelesener Caches
     */
    public function GPX_import(Request $request): Response
    {
        $formSearch = $this->createForm(SupportSearchCaches::class);
        $formUpload = $this->createForm(SupportImportGPX::class);
        $amountProcessedCaches = 0;
        $amountAssignedCaches = 0;
        $amountUpdatedCaches = 0;
        $listOfAmbiguousCaches = '';

        $formUpload->handleRequest($request);

        // if ($formUpload->isSubmitted() && $formUpload->isValid()) {
        if ($formUpload->isSubmitted()) {
            $waypoints_as_array = $this->read_Xml_file_and_get_array($formUpload['gpx_file']->getData()->getRealPath());

            if (!empty($waypoints_as_array)) {
                $result = $this->check_array_for_Oc_Gc_relations($waypoints_as_array);

                $amountProcessedCaches = count($waypoints_as_array);
                $amountAssignedCaches = $result[0];
                $amountUpdatedCaches = $result[1];
                $listOfAmbiguousCaches = $result[2];
            }
        }

        try {
            $fetchedListingInfos = $this->list_all_support_listing_infos();
        } catch (Exception $exception) {
            $fetchedListingInfos = [];
        }

        try {
            $differencesDetected = $this->list_differences_table_listing_infos();
        } catch (Exception $exception) {
            $differencesDetected = [];
        }

        return $this->render(
                'backend/support/occ_gpx_import.html.twig', [
                        'supportCachesForm' => $formSearch->createView(),
                        'supportUploadGPXForm' => $formUpload->createView(),
                        'amountProcessedCaches' => $amountProcessedCaches,
                        'amountAssignedCaches' => $amountAssignedCaches,
                        'amountUpdatedCaches' => $amountUpdatedCaches,
                        'listOfAmbiguousCaches' => $listOfAmbiguousCaches,
                        'fetchedListingInfos' => $fetchedListingInfos,
                        'differencesDetected' => $differencesDetected
                ]
        );
    }

    /**
     * @throws RecordNotFoundException
     * @throws RecordsNotFoundException
     * @throws Exception
     *
     * Unterschiede zwischen importierten Caches und deren OC-Pendants herausfinden
     */
    public function list_differences_table_listing_infos(): array
    {
        $fetchedListingInfos = $this->supportListingInfosRepository->fetchAll();
        $differencesDetected = [];

        foreach ($fetchedListingInfos as $fetchedListingInfo) {
            $fetchedOCCache = $this->cachesRepository->fetchOneBy(['wp_oc' => $fetchedListingInfo->wpOc]);
            $tempArray = [$fetchedListingInfo->wpOc . '/' . $fetchedListingInfo->nodeListingWp];

            if ($fetchedOCCache->name != $fetchedListingInfo->nodeListingName) {
                $tempArray[] = $fetchedOCCache->name . ' != ' . $fetchedListingInfo->nodeListingName;
            } else {
                $tempArray[] = '';
            }

            if ($fetchedOCCache->difficulty != $fetchedListingInfo->nodeListingDifficulty) {
                $tempArray[] = $fetchedOCCache->difficulty / 2 . ' != ' . $fetchedListingInfo->nodeListingDifficulty / 2;
            } else {
                $tempArray[] = '';
            }

            if ($fetchedOCCache->terrain != $fetchedListingInfo->nodeListingTerrain) {
                $tempArray[] = $fetchedOCCache->terrain / 2 . ' != ' . $fetchedListingInfo->nodeListingTerrain / 2;
            } else {
                $tempArray[] = '';
            }

            if (round($fetchedOCCache->longitude * 10000) != round($fetchedListingInfo->nodeListingCoordinatesLon * 10000)) {
                $tempArray[] = $fetchedOCCache->longitude . ' != ' . $fetchedListingInfo->nodeListingCoordinatesLon;
            } else {
                $tempArray[] = '';
            }

            if (round($fetchedOCCache->latitude * 10000) != round($fetchedListingInfo->nodeListingCoordinatesLat * 10000)) {
                $tempArray[] = $fetchedOCCache->latitude . ' != ' . $fetchedListingInfo->nodeListingCoordinatesLat;
            } else {
                $tempArray[] = '';
            }

            if (($fetchedListingInfo->nodeListingAvailable && ($fetchedOCCache->status != 1))
                    || (!$fetchedListingInfo->nodeListingAvailable
                            && ($fetchedOCCache->status == 1))
            ) {
                $tempArray[] = 'OC status != import status';
            } else {
                $tempArray[] = '';
            }

            if (($fetchedListingInfo->nodeListingArchived && ($fetchedOCCache->status != 3))
                    || (!$fetchedListingInfo->nodeListingAvailable
                            && ($fetchedOCCache->status == 3))
            ) {
                $tempArray[] = 'OC status != import status';
            } else {
                $tempArray[] = '';
            }

            if (count($tempArray) != 1) {
                $differencesDetected[] = $tempArray;
            }
        }

        return ($differencesDetected);
    }

    public function list_all_support_listing_infos(): array
    {
        try {
            return ($this->supportListingInfosRepository->fetchAll());
        } catch (Exception $exception) {
            return ([]);
        }
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws RecordAlreadyExistsException
     * @throws RecordNotFoundException
     * @throws RecordNotPersistedException
     */
    public function check_array_for_Oc_Gc_relations(array $waypoints_as_array): array
    {
        // Jeden importierten Cache im Array einzeln durchgehen..
        // in DB caches/* prüfen, ob ein OC-Cache zugeordnet werden kann
        //   wenn ja:
        //      Wegpunkt des OC-Caches im aktuell geprüften Cache/Array reinschreiben
        //      in DB support_listing_info schauen, ob bereits ein gleicher Eintrag wie der aktuelle Cache/Array vorhanden ist
        //        wenn ja:
        //          Unterschiede auslesen und in support_listing_comments/comment schreiben
        //          alten Eintrag löschen
        //          neuen Eintrag in support_listing_info schreiben
        //        wenn nein:
        //          -
        //   wenn nein:
        //      Bearbeitung des aktuell geprüften Caches/Array beenden
        //
        $amountAssignedCaches = 0;
        $amountUpdatedCaches = 0;
        $fetchedWpGcCaches = [];
        $fetchedWpGcMaintainedCaches = [];
        $listOfAmbiguousCaches = '';

        foreach ($waypoints_as_array as $wpt) {
            try {
                $fetchedWpGcCaches = $this->cachesRepository->fetchBy(['wp_gc' => $wpt['node_listing_wp']]);
                $fetchedWpGcMaintainedCaches = $this->cachesRepository->fetchBy(['wp_gc_maintained' => $wpt['node_listing_wp']]);
            } catch (Exception $exception) {
            }

            if (count($fetchedWpGcCaches) == 1) {
                $wpt['wp_oc'] = $fetchedWpGcCaches[0]->wpOc;
            } elseif (count($fetchedWpGcMaintainedCaches) == 1) {
                $wpt['wp_oc'] = $fetchedWpGcMaintainedCaches[0]->wpOc;
            } elseif (count($fetchedWpGcCaches) > 1 or count($fetchedWpGcMaintainedCaches) > 1) {
                $listOfAmbiguousCaches .= $wpt['node_listing_wp'] . ', ';
            }

            if ($wpt['wp_oc'] != -1) {
                $fetchedExistingSupportListingInfoArray = [];
                $fetchedExistingSupportListingInfo = [];
                $newComment = '';

                try {
                    $fetchedExistingSupportListingInfo =
                            $this->supportListingInfosRepository->fetchOneBy(['node_listing_id' => $wpt['node_listing_id']]);
                    $fetchedExistingSupportListingInfoArray =
                            $this->supportListingInfosRepository->getDatabaseArrayFromEntity($fetchedExistingSupportListingInfo);
                } catch (Exception $exception) {
                }

                if (!empty($fetchedExistingSupportListingInfoArray)) {
                    foreach (
                            [
                                    'node_listing_name',
                                    'node_listing_size',
                                    'node_listing_difficulty',
                                    'node_listing_terrain',
                                    'node_listing_coordinates_lon',
                                    'node_listing_coordinates_lat',
                                    'node_listing_available',
                                    'node_listing_archived'
                            ] as $checkItem
                    ) {
                        if ($wpt[$checkItem] != $fetchedExistingSupportListingInfoArray[$checkItem]) {
                            $newComment .= $checkItem
                                    . ' changed from '
                                    . $fetchedExistingSupportListingInfoArray[$checkItem]
                                    . ' to '
                                    . $wpt[$checkItem]
                                    . PHP_EOL;
                        }
                    }

                    if ($newComment != '') {
                        $newComment =
                                date('Y-m-d H:i:s')
                                . ' -automatically generated comment-:'
                                . PHP_EOL
                                . 'Data import from foreign node via GPX for '
                                . $fetchedExistingSupportListingInfoArray['node_listing_wp']
                                . PHP_EOL
                                . $newComment
                                . PHP_EOL;

                        try {
                            $entity = $this->supportListingCommentsRepository->fetchOneBy(['wp_oc' => $wpt['wp_oc']]);
                        } catch (Exception $exception) {
                            $entity = $this->supportListingCommentsRepository->create(new SupportListingCommentsEntity($wpt['wp_oc']));
                        }

                        $entity->comment = $newComment . $entity->comment;
                        $this->supportListingCommentsRepository->update($entity);
                    }

                    $this->supportListingInfosRepository->remove($fetchedExistingSupportListingInfo);
                }

                $wpt['importstatus'] = self::IMPORT_STATUS_FINISHED;
                $entity = $this->supportListingInfosRepository->getEntityFromDatabaseArray($wpt);
                $this->supportListingInfosRepository->create($entity);

                $amountAssignedCaches++;
            }
        }

        return ([$amountAssignedCaches, $amountUpdatedCaches, $listOfAmbiguousCaches]);
    }

    /**
     * @throws Exception
     * @throws RecordNotFoundException
     */
    public function read_Xml_file_and_get_array(string $filemane): array
    {
        $strData = file_get_contents($filemane);
        $strData = str_replace(['<groundspeak:', '</groundspeak:'], ['<', '</'], $strData);

        if ($strData === false || $strData === '') {
            return ([]);
        } else {
            $objXmlDocument = simplexml_load_string($strData);

            $objJsonDocument = json_encode($objXmlDocument);
            $arrOutput = json_decode($objJsonDocument, true);

            // Array auseinandernehmen und in ein datenbankfähiges Format umwandeln (für Tabelle support_listing_infos)
            $waypoints_as_array = [];

            foreach ($arrOutput['wpt'] as $wpt) {
                $wpt_array['wp_oc'] = -1;
                $wpt_array['node_id'] = $this->nodesRepository->get_id_by_prefix(substr($wpt['name'], 0, 2));
                // TODO: node_owner_id ist in Groundspeak GPX-Dateien nicht enthalten. Aber eventuell in anderen Quellen?
                $wpt_array['node_owner_id'] = 0;
                $wpt_array['node_owner_name'] = $wpt['cache']['owner'];
                $wpt_array['node_listing_id'] = substr($wpt['url'], strlen($wpt['url']) - 36, 36);
                $wpt_array['node_listing_wp'] = $wpt['name'];
                $wpt_array['node_listing_name'] = $wpt['cache']['name'];
                $wpt_array['node_listing_size'] = $wpt['cache']['container'];
                $wpt_array['node_listing_difficulty'] = $wpt['cache']['difficulty'] * 2;
                $wpt_array['node_listing_terrain'] = $wpt['cache']['terrain'] * 2;
                $wpt_array['node_listing_coordinates_lat'] = $wpt['@attributes']['lat'];
                $wpt_array['node_listing_coordinates_lon'] = $wpt['@attributes']['lon'];
                $wpt_array['node_listing_available'] = ($wpt['cache']['@attributes']['available'] === "True") ? 1 : 0;
                $wpt_array['node_listing_archived'] = ($wpt['cache']['@attributes']['archived'] === "True") ? 1 : 0;
                $wpt_array['importstatus'] = self::IMPORT_STATUS_NEW;

                $waypoints_as_array[] = $wpt_array;
            }

            return ($waypoints_as_array);
        }
    }
}
