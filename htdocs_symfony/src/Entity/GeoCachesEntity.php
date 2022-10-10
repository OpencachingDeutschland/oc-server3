<?php

namespace Oc\Entity;

use DateTime;
use Oc\Repository\AbstractEntity;

/**
 * Class GeoCachesEntity
 *
 * @package Oc\Entity
 */
class GeoCachesEntity extends AbstractEntity
{
    public int $cacheId;

    public string $uuid;

    public int $node;

    public DateTime $dateCreated;

    public int $isPublishdate;

    public DateTime $lastModified;

    public string $okapiSyncbase;

    public DateTime $listingLastModified;

    public DateTime $metaLastModified;

    public int $userId;

    public string $name;

    public float $longitude;

    public float $latitude;

    public int $type;

    public int $status;

    public string $country;

    public DateTime $dateHidden;

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

    public DateTime $dateActivate;

    public int $needNpaRecalc;

    public int $showCachelists;

    public int $protectOldCoords;

    public int $needsMaintenance;

    public int $listingOutdated;

    public DateTime $flagsLastModified;

    public UserEntity $user;

    public GeoCacheSizeEntity $cacheSize;

    public GeoCacheStatusEntity $cacheStatus;

    public GeoCacheTypeEntity $cacheType;

    public function isNew(): bool
    {
        return $this->cacheId === null;
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
