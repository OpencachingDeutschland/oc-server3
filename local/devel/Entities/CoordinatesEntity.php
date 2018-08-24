<?php 

class CoordinatesEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $id;

    /** @var DateTime */
    public $dateCreated;

    /** @var DateTime */
    public $lastModified;

    /** @var int */
    public $type;

    /** @var int */
    public $subtype;

    /** @var float */
    public $latitude;

    /** @var float */
    public $longitude;

    /** @var int */
    public $cacheId;

    /** @var int */
    public $userId;

    /** @var int */
    public $logId;

    /** @var string */
    public $description;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->id === null;
    }
}
