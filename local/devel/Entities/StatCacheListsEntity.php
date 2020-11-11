<?php

class StatCacheListsEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $cacheListId;

    /** @var int */
    public $entries;

    /** @var int */
    public $watchers;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->cacheListId === null;
    }
}
