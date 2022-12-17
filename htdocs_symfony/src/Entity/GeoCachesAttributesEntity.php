<?php

declare(strict_types=1);

namespace Oc\Entity;

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
