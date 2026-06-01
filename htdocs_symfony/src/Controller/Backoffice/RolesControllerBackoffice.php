<?php

declare(strict_types=1);

namespace Oc\Controller\Backoffice;

use Doctrine\DBAL\Exception;
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
use Symfony\Component\Routing\Attribute\Route;

/**
 * @Security("is_granted('ROLE_TEAM')")
 */
class RolesControllerBackoffice extends AbstractController
{
    private SecurityRolesRepository $securityRolesRepository;

    private UserRepository $userRepository;

    private UserRolesRepository $userRolesRepository;

    public function __construct(
            SecurityRolesRepository $securityRolesRepository,
            UserRepository $userRepository,
            UserRolesRepository $userRolesRepository
    ) {
        $this->securityRolesRepository = $securityRolesRepository;
        $this->userRepository = $userRepository;
        $this->userRolesRepository = $userRolesRepository;
    }

    /**
     * @throws Exception
     * @throws RecordsNotFoundException
     * @Security("is_granted('ROLE_TEAM')")
     */
    #[Route("/roles", name: "roles_index")]
    public function rolesController_index(Request $request): Response
    {
        $allRoles = $this->securityRolesRepository->fetchAll();

        return $this->render(
                'backoffice/roles/index.html.twig', ['allRoles' => $allRoles]
        );
    }

    /**
     * @throws Exception
     * @throws RecordNotFoundException
     * @throws RecordsNotFoundException
     * @Security("is_granted('ROLE_TEAM')")
     *
     * Get all users (and their roles) who are at least the given ROLE_
     */
    #[Route("/roles/teamlist", name: "roles_teamlist")]
    public function getTeamOverview(): Response
    {
        $teamMembersAndRoles = $this->userRolesRepository->getTeamMembersAndRoles('ROLE_TEAM');
        $roleNames = $this->securityRolesRepository->fetchAll();

        // no need for ROLE_USER
        foreach ($roleNames as $i => $role) {
            if ($role->role == 'ROLE_USER') {
                unset($roleNames[$i]);
            }
        }

        return $this->render(
                'backoffice/roles/team.roles.html.twig', ['teamAndRoles' => $teamMembersAndRoles, 'roleNames' => $roleNames]
        );
    }

    /**
     * @throws Exception
     * @throws RecordNotFoundException
     * @throws RecordsNotFoundException
     * @Security("is_granted('ROLE_TEAM')")
     */
    #[Route("/roles/search", name: "roles_search")]
    public function teamRolesAssignmentUserSearch(Request $request): Response
    {
        $form = $this->createForm(RolesSearchUser::class);
        $roleNames = $this->securityRolesRepository->fetchAll();
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
     * @throws Exception
     * @throws RecordNotFoundException
     * @throws RecordsNotFoundException
     * @Security("is_granted('ROLE_SUPPORT_HEAD') or is_granted('ROLE_SOCIAL_HEAD') or is_granted('ROLE_DEVELOPER_HEAD')")
     */
    #[Route("/roles/removeRole/{userId}&{role}", name: "roles_remove_role")]
    public function teamRolesRemoveRole(int $userId, string $role): Response
    {
        $form = $this->createForm(RolesSearchUser::class);
        $roleNames = $this->securityRolesRepository->fetchAll();
        $neededRole = $this->userRolesRepository->getNeededRole($role);

        if ($this->isGranted($neededRole)) {
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
     * @throws Exception
     * @throws RecordAlreadyExistsException
     * @throws RecordNotFoundException
     * @throws RecordsNotFoundException
     * @Security("is_granted('ROLE_SUPPORT_HEAD') or is_granted('ROLE_SOCIAL_HEAD') or is_granted('ROLE_DEVELOPER_HEAD')")
     */
    #[Route("/roles/promoteRole/{userId}&{role}", name: "roles_promote_role")]
    public function teamRolesPromoteRole(int $userId, string $role): Response
    {
        $form = $this->createForm(RolesSearchUser::class);
        $roleNames = $this->securityRolesRepository->fetchAll();
        $neededRole = $this->userRolesRepository->getNeededRole($role);

        if ($this->isGranted($neededRole)) {
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
