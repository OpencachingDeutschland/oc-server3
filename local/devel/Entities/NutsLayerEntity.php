<?php 

class NutsLayerEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $id;

    /** @var int */
    public $level;

    /** @var string */
    public $code;

    /** @var linestring */
    public $shape;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->id === null;
    }
}
