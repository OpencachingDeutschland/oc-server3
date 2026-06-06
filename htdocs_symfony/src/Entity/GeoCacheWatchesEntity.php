<?php

declare(strict_types=1);

namespace Oc\Entity;


class GeoCacheWatchesEntity
{
    public int $cacheId = 0;

    public int $userId;

    public function isNew(): bool
    {
        return $this->cacheId === 0;
    }

    public function getCacheId(): ?int
    {
        return $this->cacheId;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }
}
