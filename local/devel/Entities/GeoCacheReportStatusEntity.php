<?php

class GeoCacheReportStatusEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $id;

    /** @var string */
    public $name;

    /** @var int */
    public $transId;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->id === null;
    }
}
