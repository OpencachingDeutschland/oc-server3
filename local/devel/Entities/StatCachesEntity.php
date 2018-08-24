<?php

class StatCachesEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $cacheId;

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

    /** @var DateTime */
    public $lastFound;

    /** @var smallint */
    public $watch;

    /** @var smallint */
    public $ignore;

    /** @var smallint */
    public $toprating;

    /** @var smallint */
    public $picture;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->cacheId === null;
    }
}
