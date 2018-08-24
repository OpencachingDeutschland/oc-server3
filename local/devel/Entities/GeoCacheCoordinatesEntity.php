<?php 

class GeoCacheCoordinatesEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $id;

    /** @var DateTime */
    public $dateCreated;

    /** @var int */
    public $cacheId;

    /** @var float */
    public $longitude;

    /** @var float */
    public $latitude;

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
