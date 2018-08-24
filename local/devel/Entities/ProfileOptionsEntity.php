<?php

class ProfileOptionsEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $id;

    /** @var string */
    public $name;

    /** @var int */
    public $transId;

    /** @var int */
    public $internalUse;

    /** @var string */
    public $defaultValue;

    /** @var string */
    public $checkRegex;

    /** @var int */
    public $optionOrder;

    /** @var string */
    public $optionInput;

    /** @var int */
    public $optionset;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->id === null;
    }
}
