<?php

class Mp3Entity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $id;

    /** @var string */
    public $uuid;

    /** @var int */
    public $node;

    /** @var DateTime */
    public $dateCreated;

    /** @var DateTime */
    public $lastModified;

    /** @var string */
    public $url;

    /** @var string */
    public $title;

    /** @var DateTime */
    public $lastUrlCheck;

    /** @var int */
    public $objectId;

    /** @var int */
    public $local;

    /** @var int */
    public $unknownFormat;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->id === null;
    }
}
