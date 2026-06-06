<?php

declare(strict_types=1);

namespace Oc\Entity;

use DateTime;

class GeoCacheCoordinatesEntity
{
    public int $id = 0;

    public DateTime $dateCreated;

    public int $cacheId;

    public float $longitude;

    public float $latitude;

    public int $restoredBy;

    public UserEntity $user;

    public function isNew(): bool
    {
        return $this->id === 0;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateCreated(): ?\DateTime
    {
        return $this->dateCreated;
    }

    public function setDateCreated(\DateTime $dateCreated): static
    {
        $this->dateCreated = $dateCreated;

        return $this;
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

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getRestoredBy(): ?int
    {
        return $this->restoredBy;
    }

    public function setRestoredBy(int $restoredBy): static
    {
        $this->restoredBy = $restoredBy;

        return $this;
    }
}
