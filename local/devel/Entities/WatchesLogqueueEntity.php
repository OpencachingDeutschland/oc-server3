<?php 

class WatchesLogqueueEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $logId;

    /** @var int */
    public $userId;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->logId === null;
    }
}
