<?php

declare(strict_types=1);

namespace Oc\Entity;

use Oc\Repository\AbstractEntity;

class GeoCachesEntity extends AbstractEntity
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
}
