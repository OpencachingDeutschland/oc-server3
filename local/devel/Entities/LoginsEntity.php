<?php

class LoginsEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $id;

    /** @var int */
    public $userId;

    /** @var string */
    public $remoteAddr;

    /** @var int */
    public $success;

    /** @var DateTime */
    public $timestamp;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->id === null;
    }
}
