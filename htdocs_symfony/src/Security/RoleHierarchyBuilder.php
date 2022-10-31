<?php

declare(strict_types=1);

namespace Oc\Security;

use Doctrine\DBAL\Exception;
use Oc\Entity\SecurityRoleHierarchyEntity;
use Oc\Entity\SecurityRolesEntity;
use Oc\Repository\Exception\RecordsNotFoundException;
use Oc\Repository\SecurityRoleHierarchyRepository;
use Oc\Repository\SecurityRolesRepository;

class RoleHierarchyBuilder
{
    private SecurityRoleHierarchyRepository $securityRoleHierarchyRepository;

    private SecurityRolesRepository $securityRolesRepository;

    public function __construct(SecurityRolesRepository $securityRolesRepository, SecurityRoleHierarchyRepository $securityRoleHierarchyRepository)
    {
        $this->securityRoleHierarchyRepository = $securityRoleHierarchyRepository;
        $this->securityRolesRepository = $securityRolesRepository;
    }

    /**
     * @throws RecordsNotFoundException
     * @throws Exception
     */
    public function build(): array
    {
        $roles = $this->securityRolesRepository->fetchAll();
        $roleDictionary = $this->createRoleDictionary($roles);
        $hierarchyEntries = $this->securityRoleHierarchyRepository->fetchAll();

        $result = [];

        /** @var SecurityRoleHierarchyEntity $hierarchyEntry */
        foreach ($hierarchyEntries as $hierarchyEntry) {
            $role = $roleDictionary[$hierarchyEntry->roleId];
            $subRole = $roleDictionary[$hierarchyEntry->subRoleId];

            $result[$role][] = $subRole;
        }

        return $result;
    }

    private function createRoleDictionary(array $roles): array
    {
        $result = [];

        /** @var SecurityRolesEntity $role */
        foreach ($roles as $role) {
            $result[$role->id] = $role->role;
        }

        return $result;
    }
}
