<?php

namespace Oc\Security;

use Symfony\Component\Security\Core\Role\RoleHierarchy;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;

class RoleHierarchyFactory
{
    /**
     * @var RoleHierarchyBuilder
     */
    private $hierarchyBuilder;

    public function __construct(RoleHierarchyBuilder $hierarchyBuilder)
    {
        $this->hierarchyBuilder = $hierarchyBuilder;
    }

    public function create(): RoleHierarchyInterface
    {
        $roleHierarchy = $this->hierarchyBuilder->build();

        return new RoleHierarchy($roleHierarchy);
    }
}
