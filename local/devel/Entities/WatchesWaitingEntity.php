<?php 

class WatchesWaitingEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $id;

    /** @var int */
    public $userId;

    /** @var int */
    public $objectId;

    /** @var int */
    public $objectType;

    /** @var DateTime */
    public $dateCreated;

    /** @var string */
    public $watchtext;

    /** @var int */
    public $watchtype;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->id === null;
    }
}
