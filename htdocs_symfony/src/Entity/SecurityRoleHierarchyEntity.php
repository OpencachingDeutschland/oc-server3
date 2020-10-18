<?php

namespace Oc\Entity;

use Oc\Repository\AbstractEntity;

class SecurityRoleHierarchyEntity extends AbstractEntity
{
    /** @var int */
    public $roleId;

    /** @var int */
    public $subRoleId;

    /**
     * @return bool
     */
    public function isNew(): bool
    {
        return $this->roleId === null;
    }
}
