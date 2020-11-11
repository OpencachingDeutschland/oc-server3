<?php

class UserOptionsEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $userId;

    /** @var int */
    public $optionId;

    /** @var int */
    public $optionVisible;

    /** @var string */
    public $optionValue;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->userId === null;
    }
}
