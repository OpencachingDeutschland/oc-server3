<?php 

class SysSessionsEntity extends Oc\Repository\AbstractEntity
{
    /** @var string */
    public $uuid;

    /** @var int */
    public $userId;

    /** @var int */
    public $permanent;

    /** @var DateTime */
    public $lastLogin;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->uuid === null;
    }
}
