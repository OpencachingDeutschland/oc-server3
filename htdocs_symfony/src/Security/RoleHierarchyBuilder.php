<?php

declare(strict_types=1);

namespace Oc\Security;

use Doctrine\DBAL\Connection;

class RoleHierarchyBuilder
{
    public function __construct(
        private Connection $connection,
    ) {}

    public function build(): array
    {
        $roles = $this->connection->createQueryBuilder()
            ->select('*')->from('security_roles')
            ->executeQuery()->fetchAllAssociative();

        $roleDict = [];
        foreach ($roles as $role) {
            $roleDict[$role['id']] = $role['role'];
        }

        $hierarchy = $this->connection->createQueryBuilder()
            ->select('*')->from('security_role_hierarchy')
            ->executeQuery()->fetchAllAssociative();

        $result = [];
        foreach ($hierarchy as $entry) {
            $role    = $roleDict[$entry['role_id']];
            $subRole = $roleDict[$entry['sub_role_id']];
            $result[$role][] = $subRole;
        }

        return $result;
    }
}
