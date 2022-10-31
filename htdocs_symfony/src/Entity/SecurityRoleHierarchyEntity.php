<?php

declare(strict_types=1);

namespace Oc\Entity;

use Oc\Repository\AbstractEntity;

class SecurityRoleHierarchyEntity extends AbstractEntity
{
    public int $roleId = 0;

    public int $subRoleId;

    public function isNew(): bool
    {
        return $this->roleId === 0;
    }
}
