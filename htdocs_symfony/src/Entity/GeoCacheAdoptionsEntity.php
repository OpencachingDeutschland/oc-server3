<?php

namespace Oc\Entity;

use DateTime;
use Oc\Repository\AbstractEntity;

/**
 * Class GeoCacheAdoptionsEntity
 *
 * @package Oc\Entity
 */
class GeoCacheAdoptionsEntity extends AbstractEntity
{
    /** @var int */
    public $id;

    /** @var int */
    public $cacheId;

    /** @var DateTime */
    public $date;

    /** @var int */
    public $fromUserId;

    /** @var int */
    public $toUserId;

    /** @var \UserEntity */
    public $fromUser;

    /** @var \UserEntity */
    public $toUser;

    /**
     * @return bool
     */
    public function isNew() : bool
    {
        return $this->id === null;
    }
}
