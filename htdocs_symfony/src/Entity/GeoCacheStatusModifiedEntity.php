<?php

declare(strict_types=1);

namespace Oc\Entity;

use DateTime;

class GeoCacheStatusModifiedEntity
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

    public function getCacheId(): ?int
    {
        return $this->cacheId;
    }

    public function getDateModified(): ?\DateTime
    {
        return $this->dateModified;
    }

    public function getOldState(): ?int
    {
        return $this->oldState;
    }

    public function setOldState(int $oldState): static
    {
        $this->oldState = $oldState;

        return $this;
    }

    public function getNewState(): ?int
    {
        return $this->newState;
    }

    public function setNewState(int $newState): static
    {
        $this->newState = $newState;

        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): static
    {
        $this->userId = $userId;

        return $this;
    }
}
