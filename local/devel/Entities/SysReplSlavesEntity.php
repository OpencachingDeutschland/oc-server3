<?php 

class SysReplSlavesEntity extends Oc\Repository\AbstractEntity
{
    /** @var smallint */
    public $id;

    /** @var string */
    public $server;

    /** @var int */
    public $active;

    /** @var int */
    public $weight;

    /** @var int */
    public $online;

    /** @var DateTime */
    public $lastCheck;

    /** @var int */
    public $timeDiff;

    /** @var string */
    public $currentLogName;

    /** @var int */
    public $currentLogPos;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->id === null;
    }
}
