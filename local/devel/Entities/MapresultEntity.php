<?php 

class MapresultEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $queryId;

    /** @var DateTime */
    public $dateCreated;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->queryId === null;
    }
}
