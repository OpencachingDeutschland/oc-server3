<?php

namespace Oc\Entity;

use DateTime;
use Oc\Repository\AbstractEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class UserEntity
 *
 * @package Oc\Entity
 */
class UserEntity extends AbstractEntity implements UserInterface
{
    public int $userId;

    public DateTime $dateCreated;

    public DateTime $lastModified;

    public DateTime $lastLogin;

    public string $username;

    public string $password;

    public string $email;

    public int $emailProblems = 0;

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
        return $this->userId === null;
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
