<?php

class OkapiClogEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $id;

    /** @var string */
    public $data;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->id === null;
    }
}
