<?php

class GeoCacheDescModifiedEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $cacheId;

    /** @var string */
    public $language;

    /** @var DateTime */
    public $dateModified;

    /** @var DateTime */
    public $dateCreated;

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

    /** @var int */
    public $restoredBy;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->cacheId === null;
    }
}
