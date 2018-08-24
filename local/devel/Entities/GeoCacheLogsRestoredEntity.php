<?php 

class GeoCacheLogsRestoredEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $id;

    /** @var DateTime */
    public $dateModified;

    /** @var int */
    public $cacheId;

    /** @var int */
    public $originalId;

    /** @var int */
    public $restoredBy;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->id === null;
    }
}
