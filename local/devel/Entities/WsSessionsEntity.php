<?php

class WsSessionsEntity extends Oc\Repository\AbstractEntity
{
    /** @var string */
    public $id;

    /** @var int */
    public $userId;

    /** @var DateTime */
    public $dateCreated;

    /** @var DateTime */
    public $lastUsage;

    /** @var int */
    public $valid;

    /** @var int */
    public $closed;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->id === null;
    }
}
