<?php

namespace Oc\Entity;

use Oc\Repository\AbstractEntity;

class SecurityRolesEntity extends AbstractEntity
{
    public int $id;

    public string $role;

    public function isNew(): bool
    {
        return $this->id === null;
    }
}
