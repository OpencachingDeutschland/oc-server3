<?php 

class SysLoginsEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $id;

    /** @var DateTime */
    public $dateCreated;

    /** @var string */
    public $remoteAddr;

    /** @var int */
    public $success;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->id === null;
    }
}
