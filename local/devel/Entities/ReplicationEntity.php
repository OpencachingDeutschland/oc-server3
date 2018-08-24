<?php

class ReplicationEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $id;

    /** @var string */
    public $module;

    /** @var DateTime */
    public $lastRun;

    /** @var int */
    public $use;

    /** @var int */
    public $prio;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->id === null;
    }
}
