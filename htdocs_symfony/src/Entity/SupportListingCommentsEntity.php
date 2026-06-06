<?php

declare(strict_types=1);

namespace Oc\Entity;


class SupportListingCommentsEntity
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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWpOc(): ?string
    {
        return $this->wpOc;
    }

    public function setWpOc(string $wpOc): static
    {
        $this->wpOc = $wpOc;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function getCommentCreated(): ?string
    {
        return $this->commentCreated;
    }

    public function setCommentCreated(string $commentCreated): static
    {
        $this->commentCreated = $commentCreated;

        return $this;
    }

    public function getCommentLastModified(): ?string
    {
        return $this->commentLastModified;
    }

    public function setCommentLastModified(string $commentLastModified): static
    {
        $this->commentLastModified = $commentLastModified;

        return $this;
    }
}
