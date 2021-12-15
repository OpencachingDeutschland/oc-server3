<?php

namespace Oc\Controller\Backend;

use Oc\Repository\Exception\RecordsNotFoundException;
use Oc\Repository\SecurityRolesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 *
 */
class RolesController extends AbstractController
{
    private $securityRolesRepository;

    public function __construct(SecurityRolesRepository $securityRolesRepository)
    {
        $this->securityRolesRepository = $securityRolesRepository;
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @throws RecordsNotFoundException
     * @Route("/roles", name="roles_index")
     */
    public function securityController_index(Request $request)
    : Response {
        $allRoles = $this->securityRolesRepository->fetchAll();

        return $this->render(
            'backend/roles/index.html.twig', ['allRoles' => $allRoles]
        );
    }
}
