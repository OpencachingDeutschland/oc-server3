<?php

class WatchesWaitingtypesEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $id;

    /** @var string */
    public $watchtype;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->id === null;
    }
}
