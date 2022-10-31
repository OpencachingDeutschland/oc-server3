<?php

declare(strict_types=1);

namespace Oc\Entity;

use DateTime;
use Oc\Repository\AbstractEntity;

class GeoCacheReportsEntity extends AbstractEntity
{
    public int $id = 0;

    public DateTime $dateCreated;

    public int $cacheid;

    public int $userid;

    public int $reason;

    public string $note;

    public int $status;

    public int $adminid;

    public string $lastmodified;

    public string $comment;

    public UserEntity $user;

    public UserEntity $admin;

    public GeoCachesEntity $cache;

    public GeoCacheReportReasonsEntity $reportReason;

    public GeoCacheReportStatusEntity $reportStatus;

    public function isNew(): bool
    {
        return $this->id === 0;
    }
}
