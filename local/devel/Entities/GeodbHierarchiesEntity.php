<?php 

class GeodbHierarchiesEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $locId;

    /** @var int */
    public $level;

    /** @var int */
    public $idLvl1;

    /** @var int */
    public $idLvl2;

    /** @var int */
    public $idLvl3;

    /** @var int */
    public $idLvl4;

    /** @var int */
    public $idLvl5;

    /** @var int */
    public $idLvl6;

    /** @var int */
    public $idLvl7;

    /** @var int */
    public $idLvl8;

    /** @var int */
    public $idLvl9;

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
