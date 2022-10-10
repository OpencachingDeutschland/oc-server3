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
    public int $id;

    public string $wpOc;

    public int $nodeId;

    public string $nodeOwnerId;

    public string $nodeListingId;

    public string $nodeListingWp;

    public string $nodeListingName;

    public int $nodeListingSize;

    public int $nodeListingDifficulty;

    public int $nodeListingTerrain;

    public float $nodeListingCoordinatesLon;

    public float $nodeListingCoordinatesLat;

    public bool $nodeListingAvailable;

    public bool $nodeListingArchived;

    public DateTime $lastModified;

    public int $importStatus;

    public NodesEntity $node;

    public function isNew(): bool
    {
        return $this->id === null;
    }
}
