<?php

class UserDelegatesEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $userId;

    /** @var int */
    public $node;

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
