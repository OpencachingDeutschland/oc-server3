<?php

namespace Oc\Entity;

use Oc\Repository\AbstractEntity;

class SecurityRoleHierarchyEntity extends AbstractEntity
{
    public int $roleId;

    public int $subRoleId;

    public function isNew(): bool
    {
        return $this->roleId === null;
    }
}
