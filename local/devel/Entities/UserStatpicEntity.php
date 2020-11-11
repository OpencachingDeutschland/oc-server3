<?php

class UserStatpicEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $userId;

    /** @var string */
    public $lang;

    /** @var DateTime */
    public $dateCreated;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->userId === null;
    }
}
