<?php 

class GeodbCoordinatesEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $locId;

    /** @var float */
    public $lon;

    /** @var float */
    public $lat;

    /** @var int */
    public $coordType;

    /** @var int */
    public $coordSubtype;

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
