<?php

namespace Oc\Controller\Backend;

use Doctrine\DBAL\Connection;
use Oc\Repository\Exception\RecordsNotFoundException;
use Oc\Repository\SecurityRolesRepository;
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

    /**
     * @param Connection $connection
     * @param SecurityRolesRepository $securityRolesRepository
     */
    public function __construct(
        Connection $connection,
        SecurityRolesRepository $securityRolesRepository)
    {
        $this->connection = $connection;
        $this->securityRolesRepository = $securityRolesRepository;
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
     * @throws RecordsNotFoundException
     * @Route("/roles/teamlist", name="roles_teamlist")
     * @Security("is_granted('ROLE_TEAM')")

     */
    public function getTeamOverview()
    : Response
    {
        $teamMembersAndRoles = $this->getTeamMembersAndRoles(2);
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
     * @param int $searchtext
     *
     * @return array
     */
    private function getTeamMembersAndRoles(int $searchtext)
    : array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('user_roles.user_id', 'security_roles.role', 'user.username')
            ->from('user_roles')
            ->innerJoin('user_roles', 'security_roles', 'security_roles', 'user_roles.role_id = security_roles.id')
            ->innerJoin('user_roles', 'user', 'user', 'user_roles.user_id = user.user_id')
            ->where('user_roles.role_id >= :searchTerm')
            ->setParameters(['searchTerm' => $searchtext])
            ->orderBy('security_roles.role', 'ASC');

        return $qb->execute()->fetchAll();
    }
}
