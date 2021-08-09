<?php

declare(strict_types=1);

namespace Oc\Entity;

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

    /** @var string */
    public $commentCreated;

    /** @var string */
    public $commentLastModified;

    /**
     * @param string $wpOc
     * @param string $comment
     */
    public function __construct(string $wpOc, string $comment = '')
    {
        $this->wpOc = $wpOc;
        $this->comment = $comment;
        $this->commentCreated = date('Y-m-d H:i:s');
        $this->commentLastModified = date('Y-m-d H:i:s');
    }

    /**
     * @return bool
     */
    public function isNew()
    : bool
    {
        return $this->id === null;
    }
}
