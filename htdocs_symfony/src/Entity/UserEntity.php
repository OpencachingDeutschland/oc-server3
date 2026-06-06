<?php

declare(strict_types=1);

namespace Oc\Entity;

use Symfony\Component\Security\Core\User\LegacyPasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserEntity implements UserInterface, LegacyPasswordAuthenticatedUserInterface
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

    public function getUserIdentifier(): string
    {
        return $this->getUsername();
    }

    public function eraseCredentials(): void
    {
    }

    public function getUserId(): ?int
    {
        return $this->userId;
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

    public function getLastModified(): ?string
    {
        return $this->lastModified;
    }

    public function setLastModified(string $lastModified): static
    {
        $this->lastModified = $lastModified;

        return $this;
    }

    public function getLastLogin(): ?string
    {
        return $this->lastLogin;
    }

    public function setLastLogin(string $lastLogin): static
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function isEmailProblems(): ?bool
    {
        return $this->emailProblems;
    }

    public function setEmailProblems(bool $emailProblems): static
    {
        $this->emailProblems = $emailProblems;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function isPermanentLoginFlag(): ?bool
    {
        return $this->permanentLoginFlag;
    }

    public function setPermanentLoginFlag(bool $permanentLoginFlag): static
    {
        $this->permanentLoginFlag = $permanentLoginFlag;

        return $this;
    }

    public function getActivationCode(): ?string
    {
        return $this->activationCode;
    }

    public function setActivationCode(string $activationCode): static
    {
        $this->activationCode = $activationCode;

        return $this;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(string $language): static
    {
        $this->language = $language;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getGdprDeletion(): ?bool
    {
        return $this->gdprDeletion;
    }

    public function setGdprDeletion(bool $gdprDeletion): static
    {
        $this->gdprDeletion = $gdprDeletion;

        return $this;
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }
}
