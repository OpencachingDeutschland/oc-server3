<?php

declare(strict_types=1);

namespace Oc\Entity;


class GeoCacheStatusEntity
{
    public int $id = 0;

    public string $name;

    public int $transId;

    public string $de;

    public string $en;

    public int $allowUserView;

    public int $allowOwnerEditStatus;

    public int $allowUserLog;

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

    public function getDe(): ?string
    {
        return $this->de;
    }

    public function setDe(string $de): static
    {
        $this->de = $de;

        return $this;
    }

    public function getEn(): ?string
    {
        return $this->en;
    }

    public function setEn(string $en): static
    {
        $this->en = $en;

        return $this;
    }

    public function getAllowUserView(): ?int
    {
        return $this->allowUserView;
    }

    public function setAllowUserView(int $allowUserView): static
    {
        $this->allowUserView = $allowUserView;

        return $this;
    }

    public function getAllowOwnerEditStatus(): ?int
    {
        return $this->allowOwnerEditStatus;
    }

    public function setAllowOwnerEditStatus(int $allowOwnerEditStatus): static
    {
        $this->allowOwnerEditStatus = $allowOwnerEditStatus;

        return $this;
    }

    public function getAllowUserLog(): ?int
    {
        return $this->allowUserLog;
    }

    public function setAllowUserLog(int $allowUserLog): static
    {
        $this->allowUserLog = $allowUserLog;

        return $this;
    }
}
