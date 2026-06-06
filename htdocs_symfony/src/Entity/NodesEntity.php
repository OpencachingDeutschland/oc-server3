<?php

declare(strict_types=1);

namespace Oc\Entity;


class NodesEntity
{
    public int $id = 0;

    public string $name;

    public string $url;

    public string $waypointPrefix;

    public function isNew(): bool
    {
        return $this->id === 0;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

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

    public function getWaypointPrefix(): ?string
    {
        return $this->waypointPrefix;
    }

    public function setWaypointPrefix(string $waypointPrefix): static
    {
        $this->waypointPrefix = $waypointPrefix;

        return $this;
    }
}
