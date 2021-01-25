<?php

namespace Oc\Entity;

use Oc\Repository\AbstractEntity;

class CachesEntity extends AbstractEntity
{
    /** @var int */
    public $cacheId;

    /** @var string */
    public $uuid;

    /** @var int */
    public $node;

    /** @var DateTime */
    public $dateCreated;

    /** @var int */
    public $isPublishdate;

    /** @var DateTime */
    public $lastModified;

    /** @var string */
    public $okapiSyncbase;

    /** @var DateTime */
    public $listingLastModified;

    /** @var DateTime */
    public $metaLastModified;

    /** @var int */
    public $userId;

    /** @var string */
    public $name;

    /** @var float */
    public $longitude;

    /** @var float */
    public $latitude;

    /** @var int */
    public $type;

    /** @var int */
    public $status;

    /** @var string */
    public $country;

    /** @var DateTime */
    public $dateHidden;

    /** @var int */
    public $size;

    /** @var int */
    public $difficulty;

    /** @var int */
    public $terrain;

    /** @var string */
    public $logpw;

    /** @var float */
    public $searchTime;

    /** @var float */
    public $wayLength;

    /** @var string */
    public $wpGc;

    /** @var string */
    public $wpGcMaintained;

    /** @var string */
    public $wpNc;

    /** @var string */
    public $wpOc;

    /** @var string */
    public $descLanguages;

    /** @var string */
    public $defaultDesclang;

    /** @var DateTime */
    public $dateActivate;

    /** @var int */
    public $needNpaRecalc;

    /** @var int */
    public $showCachelists;

    /** @var int */
    public $protectOldCoords;

    /** @var int */
    public $needsMaintenance;

    /** @var int */
    public $listingOutdated;

    /** @var DateTime */
    public $flagsLastModified;

    /**
     * @return bool
     */
    public function isNew()
    : bool
    {
        return $this->cacheId === null;
    }

    public function isActiveAndFindable()
    : bool
    {
        if ($this->status == 1) {
            return true;
        } else {
            return false;
        }
    }
}
