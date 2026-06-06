<?php

declare(strict_types=1);

namespace Oc\Entity;


class GeoCacheReportStatusEntity
{
    public int $id = 0;

    public string $name;

    public int $transId;

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

    public function getTransId(): ?int
    {
        return $this->transId;
    }

    public function setTransId(int $transId): static
    {
        $this->transId = $transId;

        return $this;
    }
}
