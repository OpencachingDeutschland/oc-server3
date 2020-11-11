<?php

class GeodbSearchEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $id;

    /** @var int */
    public $locId;

    /** @var string */
    public $sort;

    /** @var string */
    public $simple;

    /** @var int */
    public $simplehash;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->id === null;
    }
}
