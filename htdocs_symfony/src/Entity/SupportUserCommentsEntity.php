<?php

declare(strict_types=1);

namespace Oc\Entity;

use DateTime;
use Oc\Repository\AbstractEntity;

/**
 * Class SupportUserCommentsEntity
 *
 * @package Oc\Entity
 */
class SupportUserCommentsEntity extends AbstractEntity
{
    /** @var int */
    public $id;

    /** @var int */
    public $ocUserId;

    /** @var string */
    public $comment;

    /** @var DateTime */
    public $commentCreated;

    /** @var string */
    public $commentCreatedBy;

    /** @var DateTime */
    public $commentLastModified;

    /**
     * @return bool
     */
    public function isNew()
    : bool
    {
        return $this->id === null;
    }
}
