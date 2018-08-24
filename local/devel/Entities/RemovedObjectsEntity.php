<?php

class RemovedObjectsEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $id;

    /** @var int */
    public $localID;

    /** @var string */
    public $uuid;

    /** @var int */
    public $type;

    /** @var DateTime */
    public $removedDate;

    /** @var int */
    public $node;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->id === null;
    }
}
