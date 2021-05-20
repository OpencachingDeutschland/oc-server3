<?php

namespace Oc\Entity;

use DateTime;
use Oc\Repository\AbstractEntity;

/**
 * Class GeoCacheCoordinatesEntity
 *
 * @package Oc\Entity
 */
class GeoCacheCoordinatesEntity extends AbstractEntity
{
    /** @var int */
    public $id;

    /** @var DateTime */
    public $dateCreated;

    /** @var int */
    public $cacheId;

    /** @var float */
    public $longitude;

    /** @var float */
    public $latitude;

    /** @var int */
    public $restoredBy;

    /** @var UserEntity */
    public $user;

    /**
     * @return bool
     */
    public function isNew() : bool
    {
        return $this->id === null;
    }
}
