<?php

class AttributeGroupsEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $id;

    /** @var int */
    public $categoryId;

    /** @var string */
    public $name;

    /** @var int */
    public $transId;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->id === null;
    }
}
