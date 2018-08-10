<?php 

class CountriesOptionsEntity extends Oc\Repository\AbstractEntity
{
    /** @var string */
    public $country;

    /** @var int */
    public $display;

    /** @var float */
    public $gmLat;

    /** @var float */
    public $gmLon;

    /** @var int */
    public $gmZoom;

    /** @var int */
    public $nodeId;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->country === null;
    }
}
