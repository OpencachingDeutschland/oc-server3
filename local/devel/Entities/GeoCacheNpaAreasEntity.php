<?php 

class GeoCacheNpaAreasEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $cacheId;

    /** @var int */
    public $npaId;

    /** @var int */
    public $calculated;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->cacheId === null;
    }
}
