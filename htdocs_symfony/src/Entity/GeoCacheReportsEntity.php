<?php

declare(strict_types=1);

namespace Oc\Entity;

use DateTime;

class GeoCacheReportsEntity
{
    public int $id = 0;

    public DateTime $dateCreated;

    public int $cacheid;

    public int $userid;

    public int $reason;

    public string $note;

    public int $status;

    public int $adminid;

    public string $lastmodified;

    public string $comment;

    public UserEntity $user;

    public UserEntity $admin;

    public GeoCachesEntity $cache;

    public GeoCacheReportReasonsEntity $reportReason;

    public GeoCacheReportStatusEntity $reportStatus;

    public function isNew(): bool
    {
        return $this->id === 0;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateCreated(): ?\DateTime
    {
        return $this->dateCreated;
    }

    public function setDateCreated(\DateTime $dateCreated): static
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    public function getCacheid(): ?int
    {
        return $this->cacheid;
    }

    public function setCacheid(int $cacheid): static
    {
        $this->cacheid = $cacheid;

        return $this;
    }

    public function getUserid(): ?int
    {
        return $this->userid;
    }

    public function setUserid(int $userid): static
    {
        $this->userid = $userid;

        return $this;
    }

    public function getReason(): ?int
    {
        return $this->reason;
    }

    public function setReason(int $reason): static
    {
        $this->reason = $reason;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(string $note): static
    {
        $this->note = $note;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getAdminid(): ?int
    {
        return $this->adminid;
    }

    public function setAdminid(int $adminid): static
    {
        $this->adminid = $adminid;

        return $this;
    }

    public function getLastmodified(): ?string
    {
        return $this->lastmodified;
    }

    public function setLastmodified(string $lastmodified): static
    {
        $this->lastmodified = $lastmodified;

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
}
