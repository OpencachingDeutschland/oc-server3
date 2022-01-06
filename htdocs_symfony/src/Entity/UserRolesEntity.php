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
    /** @var int */
    public $id;

    /** @var int */
    public $userId;

    /** @var int */
    public $roleId;

    /**
     * @return bool
     */
    public function isNew()
    : bool
    {
        return $this->id === null;
    }
}
