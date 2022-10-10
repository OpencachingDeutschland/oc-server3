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
    public int $id;

    public DateTime $dateCreated;

    public int $cacheId;

    public float $longitude;

    public float $latitude;

    public int $restoredBy;

    public UserEntity $user;

    public function isNew(): bool
    {
        return $this->id === null;
    }
}
