<?php

declare(strict_types=1);

namespace Oc\Entity;


class SecurityRolesEntity
{
    public int $id = 0;

    public string $role;

    public function isNew(): bool
    {
        return $this->id === 0;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): static
    {
        $this->role = $role;

        return $this;
    }
}
