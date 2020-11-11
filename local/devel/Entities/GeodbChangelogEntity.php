<?php

class GeodbChangelogEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $id;

    /** @var DateTime */
    public $datum;

    /** @var string */
    public $beschreibung;

    /** @var string */
    public $autor;

    /** @var string */
    public $version;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->id === null;
    }
}
