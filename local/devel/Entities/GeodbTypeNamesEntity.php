<?php 

class GeodbTypeNamesEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $typeId;

    /** @var string */
    public $typeLocale;

    /** @var string */
    public $name;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->typeId === null;
    }
}
