<?php 

class SysTemptablesEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $threadid;

    /** @var string */
    public $name;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->threadid === null;
    }
}
