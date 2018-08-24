<?php

class XmlsessionEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $id;

    /** @var DateTime */
    public $dateCreated;

    /** @var DateTime */
    public $lastUse;

    /** @var int */
    public $users;

    /** @var int */
    public $caches;

    /** @var int */
    public $cachedescs;

    /** @var int */
    public $cachelogs;

    /** @var int */
    public $pictures;

    /** @var int */
    public $removedobjects;

    /** @var DateTime */
    public $modifiedSince;

    /** @var int */
    public $cleaned;

    /** @var string */
    public $agent;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->id === null;
    }
}
