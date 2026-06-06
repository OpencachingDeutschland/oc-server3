<?php

declare(strict_types=1);

namespace Oc\Entity;


class UserRolesEntity
{
    public int $id = 0;

    public int $userId;

    public int $roleId;

    public function __construct(int $userId = 0, int $roleId = 0)
    {
        $this->userId = $userId;
        $this->roleId = $roleId;
    }

    public function isNew(): bool
    {
        return $this->id === 0;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getRoleId(): ?int
    {
        return $this->roleId;
    }

    public function setRoleId(int $roleId): static
    {
        $this->roleId = $roleId;

        return $this;
    }
}
