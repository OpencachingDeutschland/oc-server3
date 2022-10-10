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
    public int $id;

    public string $wpOc;

    public string $comment;

    public string $commentCreated;

    public string $commentLastModified;

    public function __construct(string $wpOc, string $comment = '')
    {
        $this->wpOc = $wpOc;
        $this->comment = $comment;
        $this->commentCreated = date('Y-m-d H:i:s');
        $this->commentLastModified = date('Y-m-d H:i:s');
    }

    public function isNew(): bool
    {
        return $this->id === null;
    }
}
