<?php

declare(strict_types=1);

namespace Oc\Controller\Backoffice;

use Doctrine\DBAL\Connection;
use Oc\Security\Auth;
use Oc\Form\RolesSearchUser;
use Oc\Repository\UserRepository;
use Oc\Repository\UserRolesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 */
class RolesControllerBackoffice extends AbstractController
{
    public function __construct(
        private Connection $connection,
        private UserRepository $userRepository,
        private UserRolesRepository $userRolesRepository,
        private Auth $auth,
    ) {}

    /**
     */
    #[Route("/roles", name: "roles_index")]
    public function rolesController_index(Request $request): Response
    {
        $allRoles = $this->connection->createQueryBuilder()
            ->select('*')->from('security_roles')->orderBy('id')
            ->executeQuery()->fetchAllAssociative();

        return $this->render(
            'backoffice/roles/index.html.twig', ['allRoles' => $allRoles]
        );
    }

    /**
     */
    #[Route("/roles/teamlist", name: "roles_teamlist")]
    public function getTeamOverview(): Response
    {
        $teamMembersAndRoles = $this->userRolesRepository->getTeamMembersAndRoles('ROLE_TEAM');
        $roleNames = $this->connection->createQueryBuilder()
            ->select('*')->from('security_roles')->orderBy('id')
            ->executeQuery()->fetchAllAssociative();

        foreach ($roleNames as $i => $role) {
            if ($role['role'] == 'ROLE_USER') {
                unset($roleNames[$i]);
            }
        }

        return $this->render(
            'backoffice/roles/team.roles.html.twig', ['teamAndRoles' => $teamMembersAndRoles, 'roleNames' => $roleNames]
        );
    }

    /**
     */
    #[Route("/roles/search", name: "roles_search")]
    public function teamRolesAssignmentUserSearch(Request $request): Response
    {
        $form = $this->createForm(RolesSearchUser::class);
        $roleNames = $this->connection->createQueryBuilder()
            ->select('*')->from('security_roles')->orderBy('id')
            ->executeQuery()->fetchAllAssociative();
        $fetchedUser = [];

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $inputData = $form->getData();
            $userId = $inputData['content_user_searchfield'];
            $fetchedUser = $this->userRepository->fetchOneById((int)$userId);
        }

        return $this->render(
            'backoffice/roles/team.assignment.html.twig', [
                'rolesUserSearchForm' => $form->createView(),
                'user_account_details' => $fetchedUser,
                'roleNames' => $roleNames
            ]
        );
    }

    /**
     */
    #[Route("/roles/removeRole/{userId}&{role}", name: "roles_remove_role")]
    public function teamRolesRemoveRole(int $userId, string $role): Response
    {
        $form = $this->createForm(RolesSearchUser::class);
        $roleNames = $this->connection->createQueryBuilder()
            ->select('*')->from('security_roles')->orderBy('id')
            ->executeQuery()->fetchAllAssociative();
        $neededRole = $this->userRolesRepository->getNeededRole($role);

        if ($this->auth->isGranted($neededRole)) {
            $this->userRolesRepository->removeRole($userId, $role);
        }

        $fetchedUser = $this->userRepository->fetchOneById($userId);

        return $this->render(
            'backoffice/roles/team.assignment.html.twig', [
                'rolesUserSearchForm' => $form->createView(),
                'user_account_details' => $fetchedUser,
                'roleNames' => $roleNames
            ]
        );
    }

    /**
     */
    #[Route("/roles/promoteRole/{userId}&{role}", name: "roles_promote_role")]
    public function teamRolesPromoteRole(int $userId, string $role): Response
    {
        $form = $this->createForm(RolesSearchUser::class);
        $roleNames = $this->connection->createQueryBuilder()
            ->select('*')->from('security_roles')->orderBy('id')
            ->executeQuery()->fetchAllAssociative();
        $neededRole = $this->userRolesRepository->getNeededRole($role);

        if ($this->auth->isGranted($neededRole)) {
            $this->userRolesRepository->grantRole($userId, $role);
        }

        $fetchedUser = $this->userRepository->fetchOneById($userId);

        return $this->render(
            'backoffice/roles/team.assignment.html.twig', [
                'rolesUserSearchForm' => $form->createView(),
                'user_account_details' => $fetchedUser,
                'roleNames' => $roleNames
            ]
        );
    }
}
