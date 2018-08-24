<?php

class GeoCacheListBookmarksEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $cacheListId;

    /** @var int */
    public $userId;

    /** @var string */
    public $password;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->cacheListId === null;
    }
}
