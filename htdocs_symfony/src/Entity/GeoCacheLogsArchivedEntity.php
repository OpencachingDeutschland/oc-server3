<?php

declare(strict_types=1);

namespace Oc\Entity;

use DateTime;
use Oc\Repository\AbstractEntity;

class GeoCacheLogsArchivedEntity extends AbstractEntity
{
    public int $id = 0;

    public string $uuid;

    public int $node;

    public string $dateCreated;

    public string $entryLastModified;

    public string $lastModified;

    public string $okapiSyncbase;

    public string $logLastModified;

    public int $cacheId;

    public int $userId;

    public int $type;

    public int $ocTeamComment;

    public string $date;

    public string $orderDate;

    public int $needsMaintenance;

    public int $listingOutdated;

    public string $text;

    public int $textHtml;

    public int $textHtmledit;

    public int $ownerNotified;

    public int $picture;

    public string $deletionDate;

    public int $deletedBy;

    public int $restoredBy;

    public function isNew(): bool
    {
        return $this->id === 0;
    }
}
