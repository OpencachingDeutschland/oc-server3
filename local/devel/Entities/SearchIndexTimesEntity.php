<?php

class SearchIndexTimesEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $objectType;

    /** @var int */
    public $objectId;

    /** @var DateTime */
    public $lastRefresh;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->objectType === null;
    }
}
