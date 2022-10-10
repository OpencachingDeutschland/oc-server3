<?php

namespace Oc\Entity;

use DateTime;
use Oc\Repository\AbstractEntity;

/**
 * Class GeoCacheStatusModifiedEntity
 *
 * @package Oc\Entity
 */
class GeoCacheStatusModifiedEntity extends AbstractEntity
{
    public int $cacheId;

    public DateTime $dateModified;

    public int $oldState;

    public int $newState;

    public int $userId;

    public UserEntity $user;

    public GeoCacheStatusEntity $cacheStatusOld;

    public GeoCacheStatusEntity $cacheStatusNew;

    public function isNew(): bool
    {
        return $this->cacheId === null;
    }
}
