<?php

class PicturesEntity extends Oc\Repository\AbstractEntity
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
    public $objectType;

    /** @var string */
    public $thumbUrl;

    /** @var DateTime */
    public $thumbLastGenerated;

    /** @var int */
    public $spoiler;

    /** @var int */
    public $local;

    /** @var int */
    public $unknownFormat;

    /** @var int */
    public $display;

    /** @var int */
    public $mappreview;

    /** @var smallint */
    public $seq;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->id === null;
    }
}
