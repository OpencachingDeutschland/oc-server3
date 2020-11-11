<?php

class MapresultDataEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $queryId;

    /** @var int */
    public $cacheId;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->queryId === null;
    }
}
