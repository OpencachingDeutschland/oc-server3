<?php

declare(strict_types=1);

namespace Oc\Entity;

use Oc\Repository\AbstractEntity;

class GeoCacheIgnoreEntity extends AbstractEntity
{
    public int $cacheId = 0;

    public int $userId;

    public function isNew(): bool
    {
        return $this->cacheId === 0;
    }
}
