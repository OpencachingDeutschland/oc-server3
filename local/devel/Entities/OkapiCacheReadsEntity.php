<?php

class OkapiCacheReadsEntity extends Oc\Repository\AbstractEntity
{
    /** @var string */
    public $cacheKey;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->cacheKey === null;
    }
}
