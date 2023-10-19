<?php

declare(strict_types=1);

namespace Oc\Entity;

use DateTime;
use Oc\Repository\AbstractEntity;

class GeoCachesModifiedEntity extends AbstractEntity
{
    public int $cacheId = 0;

    public DateTime $dateModified;

    public string $name;

    public int $type;

    public DateTime $dateHidden;

    public int $size;

    public int $difficulty;

    public int $terrain;

    public float $searchTime;

    public float $wayLength;

    public string $wpGc;

    public string $wpNc;

    public int $restoredBy;

    public function isNew(): bool
    {
        return $this->cacheId === 0;
    }
}
