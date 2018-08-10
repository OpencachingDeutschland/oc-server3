<?php 

class GeoCacheListWatchesEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $cacheListId;

    /** @var int */
    public $userId;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->cacheListId === null;
    }
}
