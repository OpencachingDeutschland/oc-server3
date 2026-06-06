<?php

declare(strict_types=1);

namespace Oc\Entity;

use DateTime;

class GeoCacheAdoptionsEntity
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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCacheId(): ?int
    {
        return $this->cacheId;
    }

    public function setCacheId(int $cacheId): static
    {
        $this->cacheId = $cacheId;

        return $this;
    }

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getFromUserId(): ?int
    {
        return $this->fromUserId;
    }

    public function setFromUserId(int $fromUserId): static
    {
        $this->fromUserId = $fromUserId;

        return $this;
    }

    public function getToUserId(): ?int
    {
        return $this->toUserId;
    }

    public function setToUserId(int $toUserId): static
    {
        $this->toUserId = $toUserId;

        return $this;
    }
}
