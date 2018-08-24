<?php

class StatCacheLogsEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $cacheId;

    /** @var int */
    public $userId;

    /** @var smallint */
    public $found;

    /** @var smallint */
    public $notfound;

    /** @var smallint */
    public $note;

    /** @var smallint */
    public $willAttend;

    /** @var smallint */
    public $maintenance;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->cacheId === null;
    }
}
