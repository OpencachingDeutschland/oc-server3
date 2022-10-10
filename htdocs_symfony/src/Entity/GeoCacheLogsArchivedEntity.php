<?php

namespace Oc\Entity;

use DateTime;
use Oc\Repository\AbstractEntity;

class GeoCacheLogsArchivedEntity extends AbstractEntity
{
    public int $id;

    public string $uuid;

    public int $node;

    public DateTime $dateCreated;

    public DateTime $entryLastModified;

    public DateTime $lastModified;

    public string $okapiSyncbase;

    public DateTime $logLastModified;

    public int $cacheId;

    public int $userId;

    public int $type;

    public int $ocTeamComment;

    public DateTime $date;

    public DateTime $orderDate;

    public int $needsMaintenance;

    public int $listingOutdated;

    public string $text;

    public int $textHtml;

    public int $textHtmledit;

    public int $ownerNotified;

    public int $picture;

    public DateTime $deletionDate;

    public int $deletedBy;

    public int $restoredBy;

    public function isNew(): bool
    {
        return $this->id === null;
    }
}
