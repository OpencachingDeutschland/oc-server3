<?php

declare(strict_types=1);

namespace Oc\Entity;

use Oc\Repository\AbstractEntity;
use Symfony\Component\Security\Core\User\UserInterface;

class UserEntity extends AbstractEntity implements UserInterface
{
    public int $userId = 0;

    public string $dateCreated;

    public string $lastModified;

    public string $lastLogin;

    public string $username;

    public string $password;

    public string $email;

    public bool $emailProblems = false;

    public float $latitude = 0;

    public float $longitude = 0;

    public bool $isActive = false;

    public string $firstname;

    public string $lastname;

    public string $country;

    public bool $permanentLoginFlag = true;

    public string $activationCode;

    public string $language = 'DE';

    public string $description = '';

    public bool $gdprDeletion = false;

    public array $roles;

    public function isNew(): bool
    {
        return $this->userId === 0;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getSalt(): string
    {
        return '';
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function eraseCredentials(): void
    {
    }
}
