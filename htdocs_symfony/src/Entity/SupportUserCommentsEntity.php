<?php

declare(strict_types=1);

namespace Oc\Entity;

use Oc\Repository\AbstractEntity;

class SupportUserCommentsEntity extends AbstractEntity
{
    public int $id = 0;

    public int $ocUserId;

    public string $comment;

    public string $commentCreated;

    public string $commentLastModified;

    public UserEntity $user;

    public function __construct(int $ocUserId, string $comment = '')
    {
        $this->ocUserId = $ocUserId;
        $this->comment = $comment;
        $this->commentCreated = date('Y-m-d H:i:s');
        $this->commentLastModified = date('Y-m-d H:i:s');
    }

    public function isNew(): bool
    {
        return $this->id === 0;
    }
}
