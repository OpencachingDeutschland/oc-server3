<?php

class Map2ResultEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $resultId;

    /** @var int */
    public $slaveId;

    /** @var int */
    public $sqlchecksum;

    /** @var string */
    public $sqlquery;

    /** @var int */
    public $sharedCounter;

    /** @var int */
    public $requestCounter;

    /** @var DateTime */
    public $dateCreated;

    /** @var DateTime */
    public $dateLastqueried;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->resultId === null;
    }
}
