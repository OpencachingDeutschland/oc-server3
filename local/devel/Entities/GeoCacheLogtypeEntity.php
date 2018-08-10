<?php 

class GeoCacheLogtypeEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $cacheTypeId;

    /** @var int */
    public $logTypeId;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->cacheTypeId === null;
    }
}
