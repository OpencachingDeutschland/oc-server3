<?php

declare(strict_types=1);

namespace Oc\Entity;

use Oc\Repository\AbstractEntity;

class SupportListingCommentsEntity extends AbstractEntity
{
    public int $id = 0;

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
        return $this->id === 0;
    }
}
