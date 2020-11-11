<?php

class Map2DataEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $resultId;

    /** @var int */
    public $cacheId;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->resultId === null;
    }
}
