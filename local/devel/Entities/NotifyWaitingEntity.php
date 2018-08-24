<?php 

class NotifyWaitingEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $id;

    /** @var int */
    public $cacheId;

    /** @var int */
    public $userId;

    /** @var int */
    public $type;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->id === null;
    }
}
