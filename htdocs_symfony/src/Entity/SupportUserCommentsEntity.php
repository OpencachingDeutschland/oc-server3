<?php

declare(strict_types=1);

namespace Oc\Entity;

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

    /** @var string */
    public $commentCreated;

    /** @var string */
    public $commentLastModified;

    /** @var UserEntity */
    public $user;

    /**
     * @param int $ocUserId
     * @param string $comment
     */
    public function __construct(int $ocUserId, string $comment = '')
    {
        $this->ocUserId = $ocUserId;
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
