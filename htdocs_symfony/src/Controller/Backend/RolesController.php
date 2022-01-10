<?php

namespace Oc\Controller\Backend;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Oc\Form\RolesSearchUser;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordsNotFoundException;
use Oc\Repository\SecurityRolesRepository;
use Oc\Repository\UserRepository;
use Oc\Repository\UserRolesRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class RolesController
 *
 * @package Oc\Controller\Backend
 * @Security("is_granted('ROLE_TEAM')")
 */
class RolesController extends AbstractController
{
    /** @var Connection */
    private $connection;

    /** @var SecurityRolesRepository */
    private $securityRolesRepository;

    /** @var UserRepository */
    private $userRepository;

    /** @var UserRolesRepository */
    private $userRolesRepository;

    /**
     * @param Connection $connection
     * @param SecurityRolesRepository $securityRolesRepository
     * @param UserRepository $userRepository
     * @param UserRolesRepository $userRolesRepository
     */
    public function __construct(
        Connection $connection,
        SecurityRolesRepository $securityRolesRepository,
        UserRepository $userRepository,
        UserRolesRepository $userRolesRepository
    ) {
        $this->connection = $connection;
        $this->securityRolesRepository = $securityRolesRepository;
        $this->userRepository = $userRepository;
        $this->userRolesRepository = $userRolesRepository;
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @throws RecordsNotFoundException
     * @Route("/roles", name="roles_index")
     * @Security("is_granted('ROLE_TEAM')")
     */
    public function rolesController_index(Request $request)
    : Response {
        $allRoles = $this->securityRolesRepository->fetchAll();

        return $this->render(
            'backend/roles/index.html.twig', ['allRoles' => $allRoles]
        );
    }

    /**
     * @return Response
     * @throws RecordNotFoundException
     * @throws RecordsNotFoundException
     * @Route("/roles/teamlist", name="roles_teamlist")
     * @Security("is_granted('ROLE_TEAM')")
     *
     * Get all users (and their roles) who are at least the given ROLE_*
     */
    public function getTeamOverview()
    : Response
    {
        $teamMembersAndRoles = $this->getTeamMembersAndRoles('ROLE_TEAM');
        $roleNames = $this->securityRolesRepository->fetchAll();

        // no need for ROLE_USER
        foreach ($roleNames as $i => $role) {
            if ($role->role == 'ROLE_USER') {
                unset($roleNames[$i]);
            }
        }

        return $this->render(
            'backend/roles/team.roles.html.twig', ['teamAndRoles' => $teamMembersAndRoles, 'roleNames' => $roleNames]
        );
    }

    /**
     * @param string $minimumRoleName
     *
     * @return array
     * @throws RecordNotFoundException
     */
    private function getTeamMembersAndRoles(string $minimumRoleName)
    : array {
        $minimumRoleId = $this->securityRolesRepository->getIdByRoleName($minimumRoleName);

        $qb = $this->connection->createQueryBuilder();
        $qb->select('user_roles.user_id', 'security_roles.role', 'user.username')
            ->from('user_roles')
            ->innerJoin('user_roles', 'security_roles', 'security_roles', 'user_roles.role_id = security_roles.id')
            ->innerJoin('user_roles', 'user', 'user', 'user_roles.user_id = user.user_id')
            ->where('user_roles.role_id >= :searchTerm')
            ->setParameters(['searchTerm' => $minimumRoleId])
            ->orderBy('security_roles.role', 'ASC');

        return $qb->execute()->fetchAll();
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @throws RecordNotFoundException|RecordsNotFoundException
     *
     * @Route("/roles/search", name="roles_search")
     * @Security("is_granted('ROLE_TEAM')")
     */
    public function teamRolesAssignmentUserSearch(Request $request)
    : Response {
        $form = $this->createForm(RolesSearchUser::class);
        $roleNames = $this->securityRolesRepository->fetchAll();
        $fetchedUser = [];

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $inputData = $form->getData();
            $userId = $inputData['content_user_searchfield'];

            $fetchedUser = $this->userRepository->fetchOneById($userId);
        }

        return $this->render(
            'backend/roles/team.assignment.html.twig', [
                                                         'rolesUserSearchForm' => $form->createView(),
                                                         'user_account_details' => $fetchedUser,
                                                         'roleNames' => $roleNames
                                                     ]
        );
    }

    /**
     * @param int $userId
     * @param string $role
     *
     * @return Response
     * @throws RecordNotFoundException
     * @throws RecordsNotFoundException
     * @Route("/roles/removeRole/{userId}&{role}", name="roles_remove_role")
     * @Security("is_granted('ROLE_SUPPORT_HEAD') or is_granted('ROLE_SOCIAL_HEAD') or is_granted('ROLE_DEVELOPER_HEAD')")
     */
    public function teamRolesRemoveRole(int $userId, string $role)
    : Response {
        $form = $this->createForm(RolesSearchUser::class);
        $roleNames = $this->securityRolesRepository->fetchAll();
        $neededRole = $this->getNeededRole($role);

        if ($this->isGranted($neededRole)) {
            $this->userRolesRepository->removeRole($userId, $role);
        }

        $fetchedUser = $this->userRepository->fetchOneById($userId);

        return $this->render(
            'backend/roles/team.assignment.html.twig', [
                                                         'rolesUserSearchForm' => $form->createView(),
                                                         'user_account_details' => $fetchedUser,
                                                         'roleNames' => $roleNames
                                                     ]
        );
    }

    /**
     * @param int $userId
     * @param string $role
     *
     * @return Response
     * @throws DBALException
     * @throws RecordAlreadyExistsException
     * @throws RecordNotFoundException|RecordsNotFoundException
     * @Route("/roles/promoteRole/{userId}&{role}", name="roles_promote_role")
     * @Security("is_granted('ROLE_SUPPORT_HEAD') or is_granted('ROLE_SOCIAL_HEAD') or is_granted('ROLE_DEVELOPER_HEAD')")
     */
    public function teamRolesPromoteRole(int $userId, string $role)
    : Response {
        $form = $this->createForm(RolesSearchUser::class);
        $roleNames = $this->securityRolesRepository->fetchAll();
        $neededRole = $this->getNeededRole($role);

        if ($this->isGranted($neededRole)) {
            $this->userRolesRepository->grantRole($userId, $role);
        }

        $fetchedUser = $this->userRepository->fetchOneById($userId);

        return $this->render(
            'backend/roles/team.assignment.html.twig', [
                                                         'rolesUserSearchForm' => $form->createView(),
                                                         'user_account_details' => $fetchedUser,
                                                         'roleNames' => $roleNames
                                                     ]
        );
    }

    /**
     * @param string $role
     *
     * @return string
     *
     * Determine which ROLE of the current user is needed to perform role changes on a user
     */
    private function getNeededRole(string $role)
    : string {
        $neededRole = '';

        if ($role === 'ROLE_TEAM') {
            $neededRole = 'ROLE_SUPER_ADMIN';
        } elseif ($role === 'ROLE_SUPER_ADMIN') {
            $neededRole = 'ROLE_SUPER_DUPER_ADMIN';
        } elseif (str_starts_with($role, 'ROLE_ADMIN')) {
            $neededRole = 'ROLE_SUPER_ADMIN';
        } elseif (str_starts_with($role, 'ROLE_SUPPORT') && (!str_ends_with($role, '_HEAD'))) {
            $neededRole = 'ROLE_SUPPORT_HEAD';
        } elseif (str_starts_with($role, 'ROLE_SOCIAL') && (!str_ends_with($role, '_HEAD'))) {
            $neededRole = 'ROLE_SOCIAL_HEAD';
        } elseif (str_starts_with($role, 'ROLE_DEVELOPER') && (!str_ends_with($role, '_HEAD'))) {
            $neededRole = 'ROLE_DEVELOPER_HEAD';
        } else {
            $neededRole = 'ROLE_ADMIN';
        }

        return $neededRole;
    }
}
