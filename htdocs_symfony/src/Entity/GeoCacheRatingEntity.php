<?php

declare(strict_types=1);

namespace Oc\Entity;

use DateTime;
use Oc\Repository\AbstractEntity;

class GeoCacheRatingEntity extends AbstractEntity
{
    public int $cacheId = 0;

    public int $userId;

    public DateTime $ratingDate;

    public function isNew(): bool
    {
        return $this->cacheId === 0;
    }
}
