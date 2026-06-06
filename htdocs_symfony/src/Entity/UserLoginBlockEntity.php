<?php

declare(strict_types=1);

namespace Oc\Entity;


class UserLoginBlockEntity
{
    public int $id = 0;

    public int $userId;

    public string $loginBlockUntil;

    public string $message;

    public function __construct(int $userId, string $loginBlockUntil, string $message)
    {
        $this->userId = $userId;
        $this->loginBlockUntil = $loginBlockUntil;
        $this->message = $message;
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

    public function getLoginBlockUntil(): ?string
    {
        return $this->loginBlockUntil;
    }

    public function setLoginBlockUntil(string $loginBlockUntil): static
    {
        $this->loginBlockUntil = $loginBlockUntil;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }
}
