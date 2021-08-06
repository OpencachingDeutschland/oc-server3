<?php

declare(strict_types=1);

namespace Oc\Entity;

use DateTime;
use Oc\Repository\AbstractEntity;

/**
 * Class SupportListingCommentsEntity
 *
 * @package Oc\Entity
 */
class SupportListingCommentsEntity extends AbstractEntity
{
    /** @var int */
    public $id;

    /** @var string */
    public $wpOc;

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
