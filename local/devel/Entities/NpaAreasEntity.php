<?php

class NpaAreasEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $id;

    /** @var string */
    public $typeId;

    /** @var int */
    public $exclude;

    /** @var string */
    public $name;

    /** @var linestring */
    public $shape;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->id === null;
    }
}
