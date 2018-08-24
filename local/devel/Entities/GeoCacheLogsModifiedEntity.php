<?php

class GeoCacheLogsModifiedEntity extends Oc\Repository\AbstractEntity
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
    public $entryLastModified;

    /** @var DateTime */
    public $lastModified;

    /** @var DateTime */
    public $logLastModified;

    /** @var int */
    public $cacheId;

    /** @var int */
    public $userId;

    /** @var int */
    public $type;

    /** @var int */
    public $ocTeamComment;

    /** @var DateTime */
    public $date;

    /** @var int */
    public $needsMaintenance;

    /** @var int */
    public $listingOutdated;

    /** @var string */
    public $text;

    /** @var int */
    public $textHtml;

    /** @var DateTime */
    public $modifyDate;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->id === null;
    }
}
