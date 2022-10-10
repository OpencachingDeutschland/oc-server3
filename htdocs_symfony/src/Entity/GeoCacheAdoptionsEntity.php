<?php

declare(strict_types=1);

namespace Oc\Entity;

use DateTime;
use Oc\Repository\AbstractEntity;

class GeoCacheAdoptionsEntity extends AbstractEntity
{
    public int $id = 0;

    public int $cacheId;

    public DateTime $date;

    public int $fromUserId;

    public int $toUserId;

    public UserEntity $fromUser;

    public UserEntity $toUser;

    public function isNew(): bool
    {
        return $this->id === 0;
    }
}
