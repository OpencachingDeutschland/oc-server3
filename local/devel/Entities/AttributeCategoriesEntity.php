<?php

class AttributeCategoriesEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $id;

    /** @var string */
    public $name;

    /** @var int */
    public $transId;

    /** @var string */
    public $color;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->id === null;
    }
}
