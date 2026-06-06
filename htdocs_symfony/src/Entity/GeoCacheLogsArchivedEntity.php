<?php

declare(strict_types=1);

namespace Oc\Entity;


class GeoCacheLogsArchivedEntity
{
    public int $id = 0;

    public string $uuid;

    public int $node;

    public string $dateCreated;

    public string $entryLastModified;

    public string $lastModified;

    public string $okapiSyncbase;

    public string $logLastModified;

    public int $cacheId;

    public int $userId;

    public int $type;

    public int $ocTeamComment;

    public string $date;

    public string $orderDate;

    public int $needsMaintenance;

    public int $listingOutdated;

    public string $text;

    public int $textHtml;

    public int $textHtmledit;

    public int $ownerNotified;

    public int $picture;

    public string $deletionDate;

    public int $deletedBy;

    public int $restoredBy;

    public function isNew(): bool
    {
        return $this->id === 0;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): static
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getNode(): ?int
    {
        return $this->node;
    }

    public function setNode(int $node): static
    {
        $this->node = $node;

        return $this;
    }

    public function getDateCreated(): ?string
    {
        return $this->dateCreated;
    }

    public function setDateCreated(string $dateCreated): static
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    public function getEntryLastModified(): ?string
    {
        return $this->entryLastModified;
    }

    public function setEntryLastModified(string $entryLastModified): static
    {
        $this->entryLastModified = $entryLastModified;

        return $this;
    }

    public function getLastModified(): ?string
    {
        return $this->lastModified;
    }

    public function setLastModified(string $lastModified): static
    {
        $this->lastModified = $lastModified;

        return $this;
    }

    public function getOkapiSyncbase(): ?string
    {
        return $this->okapiSyncbase;
    }

    public function setOkapiSyncbase(string $okapiSyncbase): static
    {
        $this->okapiSyncbase = $okapiSyncbase;

        return $this;
    }

    public function getLogLastModified(): ?string
    {
        return $this->logLastModified;
    }

    public function setLogLastModified(string $logLastModified): static
    {
        $this->logLastModified = $logLastModified;

        return $this;
    }

    public function getCacheId(): ?int
    {
        return $this->cacheId;
    }

    public function setCacheId(int $cacheId): static
    {
        $this->cacheId = $cacheId;

        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): static
    {
        $this->userId = $userId;

        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getOcTeamComment(): ?int
    {
        return $this->ocTeamComment;
    }

    public function setOcTeamComment(int $ocTeamComment): static
    {
        $this->ocTeamComment = $ocTeamComment;

        return $this;
    }

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function setDate(string $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getOrderDate(): ?string
    {
        return $this->orderDate;
    }

    public function setOrderDate(string $orderDate): static
    {
        $this->orderDate = $orderDate;

        return $this;
    }

    public function getNeedsMaintenance(): ?int
    {
        return $this->needsMaintenance;
    }

    public function setNeedsMaintenance(int $needsMaintenance): static
    {
        $this->needsMaintenance = $needsMaintenance;

        return $this;
    }

    public function getListingOutdated(): ?int
    {
        return $this->listingOutdated;
    }

    public function setListingOutdated(int $listingOutdated): static
    {
        $this->listingOutdated = $listingOutdated;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): static
    {
        $this->text = $text;

        return $this;
    }

    public function getTextHtml(): ?int
    {
        return $this->textHtml;
    }

    public function setTextHtml(int $textHtml): static
    {
        $this->textHtml = $textHtml;

        return $this;
    }

    public function getTextHtmledit(): ?int
    {
        return $this->textHtmledit;
    }

    public function setTextHtmledit(int $textHtmledit): static
    {
        $this->textHtmledit = $textHtmledit;

        return $this;
    }

    public function getOwnerNotified(): ?int
    {
        return $this->ownerNotified;
    }

    public function setOwnerNotified(int $ownerNotified): static
    {
        $this->ownerNotified = $ownerNotified;

        return $this;
    }

    public function getPicture(): ?int
    {
        return $this->picture;
    }

    public function setPicture(int $picture): static
    {
        $this->picture = $picture;

        return $this;
    }

    public function getDeletionDate(): ?string
    {
        return $this->deletionDate;
    }

    public function setDeletionDate(string $deletionDate): static
    {
        $this->deletionDate = $deletionDate;

        return $this;
    }

    public function getDeletedBy(): ?int
    {
        return $this->deletedBy;
    }

    public function setDeletedBy(int $deletedBy): static
    {
        $this->deletedBy = $deletedBy;

        return $this;
    }

    public function getRestoredBy(): ?int
    {
        return $this->restoredBy;
    }

    public function setRestoredBy(int $restoredBy): static
    {
        $this->restoredBy = $restoredBy;

        return $this;
    }
}
