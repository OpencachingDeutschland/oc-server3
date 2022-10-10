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
    public int $id;

    public int $cacheId;

    public DateTime $date;

    public int $fromUserId;

    public int $toUserId;

    public UserEntity $fromUser;

    public UserEntity $toUser;

    public function isNew(): bool
    {
        return $this->id === null;
    }
}
