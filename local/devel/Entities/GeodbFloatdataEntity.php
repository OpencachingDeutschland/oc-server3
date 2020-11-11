<?php

class GeodbFloatdataEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $locId;

    /** @var float */
    public $floatVal;

    /** @var int */
    public $floatType;

    /** @var int */
    public $floatSubtype;

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
