<?php

class LogentriesEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $id;

    /** @var DateTime */
    public $dateCreated;

    /** @var string */
    public $module;

    /** @var int */
    public $eventid;

    /** @var int */
    public $userid;

    /** @var int */
    public $objectid1;

    /** @var int */
    public $objectid2;

    /** @var string */
    public $logtext;

    /** @var string */
    public $details;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->id === null;
    }
}
