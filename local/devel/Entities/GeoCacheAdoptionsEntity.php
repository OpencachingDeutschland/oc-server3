<?php 

class GeoCacheAdoptionsEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $id;

    /** @var int */
    public $cacheId;

    /** @var DateTime */
    public $date;

    /** @var int */
    public $fromUserId;

    /** @var int */
    public $toUserId;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->id === null;
    }
}
