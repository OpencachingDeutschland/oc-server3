<?php 

class WatchesNotifiedEntity extends Oc\Repository\AbstractEntity
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

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->id === null;
    }
}
