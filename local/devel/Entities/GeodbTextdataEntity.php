<?php 

class GeodbTextdataEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $locId;

    /** @var string */
    public $textVal;

    /** @var int */
    public $textType;

    /** @var string */
    public $textLocale;

    /** @var smallint */
    public $isNativeLang;

    /** @var smallint */
    public $isDefaultName;

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
