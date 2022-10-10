<?php

declare(strict_types=1);

namespace Oc\Entity;

use Oc\Repository\AbstractEntity;

class SecurityRolesEntity extends AbstractEntity
{
    public int $id = 0;

    public string $role;

    public function isNew(): bool
    {
        return $this->id === 0;
    }
}
