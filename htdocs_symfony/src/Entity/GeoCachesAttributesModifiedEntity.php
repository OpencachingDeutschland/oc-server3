<?php

declare(strict_types=1);

namespace Oc\Entity;

use DateTime;
use Oc\Repository\AbstractEntity;

class GeoCachesAttributesModifiedEntity extends AbstractEntity
{
    public int $cacheId = 0;

    public int $attribId;

    public DateTime $dateModified;

    public int $wasSet;

    public int $restoredBy;

    public function isNew(): bool
    {
        return $this->cacheId === 0;
    }
}
