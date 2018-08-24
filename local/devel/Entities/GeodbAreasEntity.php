<?php 

class GeodbAreasEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $locId;

    /** @var int */
    public $areaId;

    /** @var int */
    public $polygonId;

    /** @var int */
    public $polSeqNo;

    /** @var smallint */
    public $excludeArea;

    /** @var int */
    public $areaType;

    /** @var int */
    public $areaSubtype;

    /** @var int */
    public $coordType;

    /** @var int */
    public $coordSubtype;

    /** @var int */
    public $resolution;

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
