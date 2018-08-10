<?php 

class GeoCacheSizeEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $id;

    /** @var string */
    public $name;

    /** @var int */
    public $transId;

    /** @var int */
    public $ordinal;

    /** @var string */
    public $de;

    /** @var string */
    public $en;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->id === null;
    }
}
