<?php

declare(strict_types=1);

namespace Oc\Entity;

use DateTime;

class GeoCacheRatingEntity
{
    public int $cacheId = 0;

    public int $userId;

    public DateTime $ratingDate;

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

    public function getRatingDate(): ?\DateTime
    {
        return $this->ratingDate;
    }

    public function setRatingDate(\DateTime $ratingDate): static
    {
        $this->ratingDate = $ratingDate;

        return $this;
    }
}
