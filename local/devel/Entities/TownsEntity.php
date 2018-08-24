<?php

class TownsEntity extends Oc\Repository\AbstractEntity
{
    /** @var string */
    public $country;

    /** @var string */
    public $name;

    /** @var int */
    public $transId;

    /** @var float */
    public $coordLat;

    /** @var float */
    public $coordLong;

    /** @var int */
    public $maplist;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->country === null;
    }
}
