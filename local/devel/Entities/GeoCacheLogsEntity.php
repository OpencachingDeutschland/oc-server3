<?php

class GeoCacheLogsEntity extends Oc\Repository\AbstractEntity
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

    /** @var string */
    public $okapiSyncbase;

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

    /** @var DateTime */
    public $orderDate;

    /** @var int */
    public $needsMaintenance;

    /** @var int */
    public $listingOutdated;

    /** @var string */
    public $text;

    /** @var int */
    public $textHtml;

    /** @var int */
    public $textHtmledit;

    /** @var int */
    public $ownerNotified;

    /** @var smallint */
    public $picture;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->id === null;
    }
}
