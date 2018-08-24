<?php 

class GkItemEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $id;

    /** @var string */
    public $name;

    /** @var string */
    public $description;

    /** @var int */
    public $userid;

    /** @var DateTime */
    public $datecreated;

    /** @var float */
    public $distancetravelled;

    /** @var float */
    public $latitude;

    /** @var float */
    public $longitude;

    /** @var int */
    public $typeid;

    /** @var int */
    public $stateid;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->id === null;
    }
}
