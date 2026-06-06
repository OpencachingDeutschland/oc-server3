<?php

declare(strict_types=1);

namespace Oc\Entity;


class SecurityRoleHierarchyEntity
{
    public int $roleId = 0;

    public int $subRoleId;

    public function isNew(): bool
    {
        return $this->roleId === 0;
    }

    public function getRoleId(): ?int
    {
        return $this->roleId;
    }

    public function getSubRoleId(): ?int
    {
        return $this->subRoleId;
    }
}
