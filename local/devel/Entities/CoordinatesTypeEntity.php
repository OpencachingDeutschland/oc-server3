<?php 

class CoordinatesTypeEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $id;

    /** @var string */
    public $name;

    /** @var int */
    public $transId;

    /** @var string */
    public $image;

    /** @var string */
    public $preposition;

    /** @var int */
    public $ppTransId;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->id === null;
    }
}
