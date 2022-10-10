<?php

declare(strict_types=1);

namespace Oc\Entity;

use Oc\Repository\AbstractEntity;

class UserRolesEntity extends AbstractEntity
{
    public int $id = 0;

    public int $userId;

    public int $roleId;

    public function __construct(int $userId = 0, int $roleId = 0)
    {
        $this->userId = $userId;
        $this->roleId = $roleId;
    }

    public function isNew(): bool
    {
        return $this->id === 0;
    }
}
