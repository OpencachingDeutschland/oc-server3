<?php

declare(strict_types=1);

namespace Oc\Entity;

use DateTime;
use Oc\Repository\AbstractEntity;

class GeoCacheVisitsEntity extends AbstractEntity
{
    public int $cacheId = 0;

    public int $userIdIP;

    public int $count;

    /** @var DateTime */
    public DateTime $lastModified;

    public function isNew(): bool
    {
        return $this->cacheId === 0;
    }
}
