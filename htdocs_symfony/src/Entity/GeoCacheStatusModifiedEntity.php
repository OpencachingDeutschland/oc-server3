<?php

namespace Oc\Entity;

use DateTime;
use Oc\Repository\AbstractEntity;

class GeoCacheStatusModifiedEntity extends AbstractEntity
{
    /** @var int */
    public $cacheId;

    /** @var DateTime */
    public $dateModified;

    /** @var int */
    public $oldState;

    /** @var int */
    public $newState;

    /** @var int */
    public $userId;

    /** @var UserEntity */
    public $user;

    /** @var GeoCacheReportStatusEntity */
    public $statusOld;

    /** @var GeoCacheReportStatusEntity */
    public $statusNew;

    /**
     * @return bool
     */
    public function isNew() : bool
    {
        return $this->cacheId === null;
    }
}
