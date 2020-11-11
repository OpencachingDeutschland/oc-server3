<?php

class GeoCacheCountriesEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $id;

    /** @var DateTime */
    public $dateCreated;

    /** @var int */
    public $cacheId;

    /** @var string */
    public $country;

    /** @var int */
    public $restoredBy;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->id === null;
    }
}
