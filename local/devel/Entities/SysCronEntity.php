<?php

class SysCronEntity extends Oc\Repository\AbstractEntity
{
    /** @var string */
    public $name;

    /** @var DateTime */
    public $lastRun;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->name === null;
    }
}
