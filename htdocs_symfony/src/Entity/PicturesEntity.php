<?php

declare(strict_types=1);

namespace Oc\Entity;

use DateTime;

class PicturesEntity
{
    public int $id = 0;

    public string $uuid;

    public int $node;

    public DateTime $dateCreated;

    public DateTime $lastModified;

    public string $url;

    public string $title;

    public DateTime $lastUrlCheck;

    public int $objectId;

    public int $objectType;

    public string $thumbUrl;

    public DateTime $thumbLastGenerated;

    public int $spoiler;

    public int $local;

    public int $unknownFormat;

    public int $display;

    public int $mappreview;

    public int $seq;

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

    public function getDateCreated(): ?\DateTime
    {
        return $this->dateCreated;
    }

    public function setDateCreated(\DateTime $dateCreated): static
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    public function getLastModified(): ?\DateTime
    {
        return $this->lastModified;
    }

    public function setLastModified(\DateTime $lastModified): static
    {
        $this->lastModified = $lastModified;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getLastUrlCheck(): ?\DateTime
    {
        return $this->lastUrlCheck;
    }

    public function setLastUrlCheck(\DateTime $lastUrlCheck): static
    {
        $this->lastUrlCheck = $lastUrlCheck;

        return $this;
    }

    public function getObjectId(): ?int
    {
        return $this->objectId;
    }

    public function setObjectId(int $objectId): static
    {
        $this->objectId = $objectId;

        return $this;
    }

    public function getObjectType(): ?int
    {
        return $this->objectType;
    }

    public function setObjectType(int $objectType): static
    {
        $this->objectType = $objectType;

        return $this;
    }

    public function getThumbUrl(): ?string
    {
        return $this->thumbUrl;
    }

    public function setThumbUrl(string $thumbUrl): static
    {
        $this->thumbUrl = $thumbUrl;

        return $this;
    }

    public function getThumbLastGenerated(): ?\DateTime
    {
        return $this->thumbLastGenerated;
    }

    public function setThumbLastGenerated(\DateTime $thumbLastGenerated): static
    {
        $this->thumbLastGenerated = $thumbLastGenerated;

        return $this;
    }

    public function getSpoiler(): ?int
    {
        return $this->spoiler;
    }

    public function setSpoiler(int $spoiler): static
    {
        $this->spoiler = $spoiler;

        return $this;
    }

    public function getLocal(): ?int
    {
        return $this->local;
    }

    public function setLocal(int $local): static
    {
        $this->local = $local;

        return $this;
    }

    public function getUnknownFormat(): ?int
    {
        return $this->unknownFormat;
    }

    public function setUnknownFormat(int $unknownFormat): static
    {
        $this->unknownFormat = $unknownFormat;

        return $this;
    }

    public function getDisplay(): ?int
    {
        return $this->display;
    }

    public function setDisplay(int $display): static
    {
        $this->display = $display;

        return $this;
    }

    public function getMappreview(): ?int
    {
        return $this->mappreview;
    }

    public function setMappreview(int $mappreview): static
    {
        $this->mappreview = $mappreview;

        return $this;
    }

    public function getSeq(): ?int
    {
        return $this->seq;
    }

    public function setSeq(int $seq): static
    {
        $this->seq = $seq;

        return $this;
    }
}
