<?php

declare(strict_types=1);

namespace Oc\Entity;

use DateTime;
use Oc\Repository\AbstractEntity;

/**
 * Class SupportListingInfosEntity
 *
 * @package Oc\Entity
 */
class SupportListingInfosEntity extends AbstractEntity
{
    /** @var int */
    public $id;

    /** @var string */
    public $wpOc;

    /** @var int */
    public $nodeId;

    /** @var string */
    public $nodeOwnerId;

    /** @var string */
    public $nodeListingId;

    /** @var string */
    public $nodeListingWp;

    /** @var string */
    public $nodeListingName;

    /** @var int */
    public $nodeListingSize;

    /** @var int */
    public $nodeListingDifficulty;

    /** @var int */
    public $nodeListingTerrain;

    /** @var double */
    public $nodeListingCoordinatesLon;

    /** @var double */
    public $nodeListingCoordinatesLat;

    /** @var bool */
    public $nodeListingAvailable;

    /** @var bool */
    public $nodeListingArchived;

    /** @var DateTime */
    public $lastModified;

    /** @var int */
    public $importStatus;

    /** @var NodesEntity */
    public $node;

    /**
     * @return bool
     */
    public function isNew()
    : bool
    {
        return $this->id === null;
    }
}
