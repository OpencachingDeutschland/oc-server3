<?php

namespace Oc\Security;

use Doctrine\DBAL\Exception;
use Oc\Repository\Exception\RecordsNotFoundException;
use Symfony\Component\Security\Core\Role\RoleHierarchy;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;

class RoleHierarchyFactory
{
    private RoleHierarchyBuilder $hierarchyBuilder;

    public function __construct(RoleHierarchyBuilder $hierarchyBuilder)
    {
        $this->hierarchyBuilder = $hierarchyBuilder;
    }

    /**
     * @throws RecordsNotFoundException
     * @throws Exception
     */
    public function create(): RoleHierarchyInterface
    {
        $roleHierarchy = $this->hierarchyBuilder->build();

        return new RoleHierarchy($roleHierarchy);
    }
}
