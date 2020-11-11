<?php

class NpaTypesEntity extends Oc\Repository\AbstractEntity
{
    /** @var string */
    public $id;

    /** @var string */
    public $name;

    /** @var int */
    public $ordinal;

    /** @var int */
    public $noWarning;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->id === null;
    }
}
