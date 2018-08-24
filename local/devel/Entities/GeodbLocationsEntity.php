<?php

class GeodbLocationsEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $locId;

    /** @var int */
    public $locType;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->locId === null;
    }
}
