<?php

namespace Oc\Entity;

use DateTime;
use Oc\Repository\AbstractEntity;

class GeoCacheReportsEntity extends AbstractEntity
{
    /** @var int */
    public $id;

    /** @var DateTime */
    public $dateCreated;

    /** @var int */
    public $cacheid;

    /** @var int */
    public $userid;

    /** @var int */
    public $reason;

    /** @var string */
    public $note;

    /** @var int */
    public $status;

    /** @var int */
    public $adminid;

    /** @var string */
    public $lastmodified;

    /** @var string */
    public $comment;

    /** @var UserEntity */
    public $user;

    /** @var UserEntity */
    public $adminName;

    /** @var GeoCachesEntity */
    public $cache;

    /** @var GeoCacheReportReasonsEntity */
    public $reportReason;

    /** @var GeoCacheReportStatusEntity */
    public $reportStatus;

    /**
     * @return bool
     */
    public function isNew() : bool
    {
        return $this->id === null;
    }
}
