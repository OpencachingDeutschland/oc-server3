<?php 

class GeoCacheVisitsEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $cacheId;

    /** @var string */
    public $userIdIp;

    /** @var smallint */
    public $count;

    /** @var DateTime */
    public $lastModified;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->cacheId === null;
    }
}
