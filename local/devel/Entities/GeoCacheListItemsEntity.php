<?php 

class GeoCacheListItemsEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $cacheListId;

    /** @var int */
    public $cacheId;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->cacheListId === null;
    }
}
