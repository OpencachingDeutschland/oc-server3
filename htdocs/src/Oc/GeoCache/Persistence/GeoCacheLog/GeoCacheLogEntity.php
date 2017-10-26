<?php

namespace Oc\GeoCache\Persistence\GeoCacheLog;

use DateTime;
use Oc\Repository\AbstractEntity;

class GeoCacheLogEntity extends AbstractEntity
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $uuid;

    /**
     * @var int
     */
    public $node;

    /**
     * @var DateTime
     */
    public $dateCreated;

    /**
     * @var DateTime
     */
    public $entryLastModified;

    /**
     * @var DateTime
     */
    public $lastModified;

    /**
     * @var int
     */
    public $okapiSyncbase;

    /**
     * @var DateTime
     */
    public $logLastModified;

    /**
     * @var int
     */
    public $cacheId;

    /**
     * @var int
     */
    public $userId;

    /**
     * @var int
     */
    public $type;

    /**
     * @var bool
     */
    public $ocTeamComment;

    /**
     * @var DateTime
     */
    public $date;

    /**
     * @var DateTime
     */
    public $orderDate;

    /**
     * @var bool
     */
    public $needsMaintenance;

    /**
     * @var bool
     */
    public $listingOutdated;

    /**
     * @var string
     */
    public $text;

    /**
     * @var bool
     */
    public $textHtml;

    /**
     * @var bool
     */
    public $textHtmledit;

    /**
     * @var bool
     */
    public $ownerNotified;

    /**
     * @var int
     */
    public $picture;

    /**
     * Checks if the entity is new.
     *
     * @return bool
     */
    public function isNew()
    {
        return $this->id === null;
    }
}
