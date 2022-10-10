<?php

declare(strict_types=1);

namespace Oc\Entity;

use Oc\Repository\AbstractEntity;

/**
 * Class UserRolesEntity
 *
 * @package Oc\Entity
 */
class UserRolesEntity extends AbstractEntity
{
    public int $id;

    public int $userId;

    public int $roleId;

    public function __construct(int $userId = 0, int $roleId = 0)
    {
        $this->userId = $userId;
        $this->roleId = $roleId;
    }

    public function isNew(): bool
    {
        return $this->id === null;
    }
}
