<?php

class GeoCacheDescEntity extends Oc\Repository\AbstractEntity
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

    /** @var int */
    public $cacheId;

    /** @var string */
    public $language;

    /** @var string */
    public $desc;

    /** @var int */
    public $descHtml;

    /** @var int */
    public $descHtmledit;

    /** @var string */
    public $hint;

    /** @var string */
    public $shortDesc;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->id === null;
    }
}
