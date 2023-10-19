<?php

declare(strict_types=1);

namespace Oc\Entity;

use Oc\Repository\AbstractEntity;

class GeoCachesAttributesEntity extends AbstractEntity
{
    public int $cacheId = 0;

    public int $attribId;

    public function isNew() :bool
    {
        return $this->cacheId === 0;
    }

    public function isOCOnly() :bool
    {
        return $this->cacheId === 6;
    }
}
