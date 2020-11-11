<?php

class GeodbIntdataEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $locId;

    /** @var int */
    public $intVal;

    /** @var int */
    public $intType;

    /** @var int */
    public $intSubtype;

    /** @var DateTime */
    public $validSince;

    /** @var int */
    public $dateTypeSince;

    /** @var DateTime */
    public $validUntil;

    /** @var int */
    public $dateTypeUntil;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->locId === null;
    }
}
