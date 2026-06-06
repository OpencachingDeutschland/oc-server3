<?php

declare(strict_types=1);

namespace Oc\Entity;


class LogTypesEntity
{
    public int $id = 0;

    public string $name;

    public int $transId;

    public string $permission;

    public int $cacheStatus;

    public string $de;

    public string $en;

    public string $iconSmall;

    public int $allowRating;

    public int $requirePassword;

    public int $maintenanceLogs;

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

    public function getPermission(): ?string
    {
        return $this->permission;
    }

    public function setPermission(string $permission): static
    {
        $this->permission = $permission;

        return $this;
    }

    public function getCacheStatus(): ?int
    {
        return $this->cacheStatus;
    }

    public function setCacheStatus(int $cacheStatus): static
    {
        $this->cacheStatus = $cacheStatus;

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

    public function getIconSmall(): ?string
    {
        return $this->iconSmall;
    }

    public function setIconSmall(string $iconSmall): static
    {
        $this->iconSmall = $iconSmall;

        return $this;
    }

    public function getAllowRating(): ?int
    {
        return $this->allowRating;
    }

    public function setAllowRating(int $allowRating): static
    {
        $this->allowRating = $allowRating;

        return $this;
    }

    public function getRequirePassword(): ?int
    {
        return $this->requirePassword;
    }

    public function setRequirePassword(int $requirePassword): static
    {
        $this->requirePassword = $requirePassword;

        return $this;
    }

    public function getMaintenanceLogs(): ?int
    {
        return $this->maintenanceLogs;
    }

    public function setMaintenanceLogs(int $maintenanceLogs): static
    {
        $this->maintenanceLogs = $maintenanceLogs;

        return $this;
    }
}
