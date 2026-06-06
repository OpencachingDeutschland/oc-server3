<?php

declare(strict_types=1);

namespace Oc\Entity;


class GeoCachesEntity
{
    public int $cacheId = 0;

    public string $uuid;

    public int $node;

    public string $dateCreated;

    public int $isPublishdate;

    public string $lastModified;

    public string $okapiSyncbase;

    public string $listingLastModified;

    public string $metaLastModified;

    public int $userId;

    public string $name;

    public float $longitude;

    public float $latitude;

    public int $type;

    public int $status;

    public string $country;

    public string $dateHidden;

    public int $size;

    public int $difficulty;

    public int $terrain;

    public string $logpw;

    public float $searchTime;

    public float $wayLength;

    public string $wpGc;

    public string $wpGcMaintained;

    public string $wpNc;

    public string $wpOc;

    public string $descLanguages;

    public string $defaultDesclang;

    public string $dateActivate;

    public int $needNpaRecalc;

    public int $showCachelists;

    public int $protectOldCoords;

    public int $needsMaintenance;

    public int $listingOutdated;

    public string $flagsLastModified;

    public bool $gdprDeletion = false;

    public UserEntity $user;

    public GeoCacheSizeEntity $cacheSize;

    public GeoCacheStatusEntity $cacheStatus;

    public GeoCacheTypeEntity $cacheType;

    // TODO: slini, neu
    public GeoCacheRatingEntity $cacheRating;

    public GeoCacheIgnoreEntity $cacheIgnore;

    public GeoCacheWatchesEntity $cacheWatches;

    public GeoCacheVisitsEntity $cacheVisits;

    public LogTypesEntity $logTypes;

    public int $ratingCount;

    public int $ignoreCount;

    public int $watchesCount;

    public int $visitsCount;

    public array $cacheLogs;

    public array $logsCount;

    public int $pictureCount;

    public array $imageName;

    // TODO Ende

    public function isNew(): bool
    {
        return $this->cacheId === 0;
    }

    public function isActiveAndFindable(): bool
    {
        if ($this->status == 1) {
            return true;
        } else {
            return false;
        }
    }

    public function getCacheId(): ?int
    {
        return $this->cacheId;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): static
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getNode(): ?int
    {
        return $this->node;
    }

    public function setNode(int $node): static
    {
        $this->node = $node;

        return $this;
    }

    public function getDateCreated(): ?string
    {
        return $this->dateCreated;
    }

    public function setDateCreated(string $dateCreated): static
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    public function getIsPublishdate(): ?int
    {
        return $this->isPublishdate;
    }

    public function setIsPublishdate(int $isPublishdate): static
    {
        $this->isPublishdate = $isPublishdate;

        return $this;
    }

    public function getLastModified(): ?string
    {
        return $this->lastModified;
    }

    public function setLastModified(string $lastModified): static
    {
        $this->lastModified = $lastModified;

        return $this;
    }

    public function getOkapiSyncbase(): ?string
    {
        return $this->okapiSyncbase;
    }

    public function setOkapiSyncbase(string $okapiSyncbase): static
    {
        $this->okapiSyncbase = $okapiSyncbase;

        return $this;
    }

    public function getListingLastModified(): ?string
    {
        return $this->listingLastModified;
    }

    public function setListingLastModified(string $listingLastModified): static
    {
        $this->listingLastModified = $listingLastModified;

        return $this;
    }

    public function getMetaLastModified(): ?string
    {
        return $this->metaLastModified;
    }

    public function setMetaLastModified(string $metaLastModified): static
    {
        $this->metaLastModified = $metaLastModified;

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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

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

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getDateHidden(): ?string
    {
        return $this->dateHidden;
    }

    public function setDateHidden(string $dateHidden): static
    {
        $this->dateHidden = $dateHidden;

        return $this;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(int $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function getDifficulty(): ?int
    {
        return $this->difficulty;
    }

    public function setDifficulty(int $difficulty): static
    {
        $this->difficulty = $difficulty;

        return $this;
    }

    public function getTerrain(): ?int
    {
        return $this->terrain;
    }

    public function setTerrain(int $terrain): static
    {
        $this->terrain = $terrain;

        return $this;
    }

    public function getLogpw(): ?string
    {
        return $this->logpw;
    }

    public function setLogpw(string $logpw): static
    {
        $this->logpw = $logpw;

        return $this;
    }

    public function getSearchTime(): ?float
    {
        return $this->searchTime;
    }

    public function setSearchTime(float $searchTime): static
    {
        $this->searchTime = $searchTime;

        return $this;
    }

    public function getWayLength(): ?float
    {
        return $this->wayLength;
    }

    public function setWayLength(float $wayLength): static
    {
        $this->wayLength = $wayLength;

        return $this;
    }

    public function getWpGc(): ?string
    {
        return $this->wpGc;
    }

    public function setWpGc(string $wpGc): static
    {
        $this->wpGc = $wpGc;

        return $this;
    }

    public function getWpGcMaintained(): ?string
    {
        return $this->wpGcMaintained;
    }

    public function setWpGcMaintained(string $wpGcMaintained): static
    {
        $this->wpGcMaintained = $wpGcMaintained;

        return $this;
    }

    public function getWpNc(): ?string
    {
        return $this->wpNc;
    }

    public function setWpNc(string $wpNc): static
    {
        $this->wpNc = $wpNc;

        return $this;
    }

    public function getWpOc(): ?string
    {
        return $this->wpOc;
    }

    public function setWpOc(string $wpOc): static
    {
        $this->wpOc = $wpOc;

        return $this;
    }

    public function getDescLanguages(): ?string
    {
        return $this->descLanguages;
    }

    public function setDescLanguages(string $descLanguages): static
    {
        $this->descLanguages = $descLanguages;

        return $this;
    }

    public function getDefaultDesclang(): ?string
    {
        return $this->defaultDesclang;
    }

    public function setDefaultDesclang(string $defaultDesclang): static
    {
        $this->defaultDesclang = $defaultDesclang;

        return $this;
    }

    public function getDateActivate(): ?string
    {
        return $this->dateActivate;
    }

    public function setDateActivate(string $dateActivate): static
    {
        $this->dateActivate = $dateActivate;

        return $this;
    }

    public function getNeedNpaRecalc(): ?int
    {
        return $this->needNpaRecalc;
    }

    public function setNeedNpaRecalc(int $needNpaRecalc): static
    {
        $this->needNpaRecalc = $needNpaRecalc;

        return $this;
    }

    public function getShowCachelists(): ?int
    {
        return $this->showCachelists;
    }

    public function setShowCachelists(int $showCachelists): static
    {
        $this->showCachelists = $showCachelists;

        return $this;
    }

    public function getProtectOldCoords(): ?int
    {
        return $this->protectOldCoords;
    }

    public function setProtectOldCoords(int $protectOldCoords): static
    {
        $this->protectOldCoords = $protectOldCoords;

        return $this;
    }

    public function getNeedsMaintenance(): ?int
    {
        return $this->needsMaintenance;
    }

    public function setNeedsMaintenance(int $needsMaintenance): static
    {
        $this->needsMaintenance = $needsMaintenance;

        return $this;
    }

    public function getListingOutdated(): ?int
    {
        return $this->listingOutdated;
    }

    public function setListingOutdated(int $listingOutdated): static
    {
        $this->listingOutdated = $listingOutdated;

        return $this;
    }

    public function getFlagsLastModified(): ?string
    {
        return $this->flagsLastModified;
    }

    public function setFlagsLastModified(string $flagsLastModified): static
    {
        $this->flagsLastModified = $flagsLastModified;

        return $this;
    }

    public function getGdprDeletion(): ?bool
    {
        return $this->gdprDeletion;
    }

    public function setGdprDeletion(bool $gdprDeletion): static
    {
        $this->gdprDeletion = $gdprDeletion;

        return $this;
    }
}
