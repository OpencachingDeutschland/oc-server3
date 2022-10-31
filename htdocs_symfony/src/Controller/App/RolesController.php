<?php

declare(strict_types=1);

namespace Oc\Controller\App;

use Oc\Repository\SecurityRolesRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Security("is_granted('ROLE_TEAM')")
 */
class RolesController extends AbstractController
{
    private SecurityRolesRepository $securityRolesRepository;

    public function __construct(SecurityRolesRepository $securityRolesRepository)
    {
        $this->securityRolesRepository = $securityRolesRepository;
    }
}
