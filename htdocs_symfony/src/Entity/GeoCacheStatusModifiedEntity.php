<?php

declare(strict_types=1);

namespace Oc\Entity;

use DateTime;
use Oc\Repository\AbstractEntity;

class GeoCacheStatusModifiedEntity extends AbstractEntity
{
    public int $cacheId = 0;

    public DateTime $dateModified;

    public int $oldState;

    public int $newState;

    public int $userId;

    public UserEntity $user;

    public GeoCacheStatusEntity $cacheStatusOld;

    public GeoCacheStatusEntity $cacheStatusNew;

    public function isNew(): bool
    {
        return $this->cacheId === 0;
    }
}
