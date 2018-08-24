<?php

class LogentriesTypesEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $id;

    /** @var string */
    public $module;

    /** @var string */
    public $eventname;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->id === null;
    }
}
