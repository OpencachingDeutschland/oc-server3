<?php

class GeodbPolygonsEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $polygonId;

    /** @var int */
    public $seqNo;

    /** @var float */
    public $lon;

    /** @var float */
    public $lat;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->polygonId === null;
    }
}
