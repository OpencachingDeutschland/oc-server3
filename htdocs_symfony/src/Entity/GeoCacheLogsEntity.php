<?php

declare(strict_types=1);

namespace Oc\Entity;

use DateTime;
use Oc\Repository\AbstractEntity;

class GeoCacheLogsEntity extends AbstractEntity
{
    public int $id = 0;

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

    public bool $gdprDeletion;

    public LogTypesEntity $logType;

    public UserEntity $user;

    public array $pictures;

    public bool $ratingCacheLog;

    public function isNew(): bool
    {
        return $this->id === 0;
    }
}
