<?php

namespace App\Domain\Auth\Entity\UserAuth;

use JsonSerializable;

class UserAuthEntity implements JsonSerializable
{
    public function __construct(
        private ?int $userId = null,
        private ?string $email = null,
        private ?string $password = null,
    ) {
    }

    public function jsonSerialize(): array
    {
        return [
            'userId' => $this->userId,
            'email' => $this->email,
            'password' => $this->password,
        ];
    }
    public function getUserId(): ?int
    {
        return $this->userId;
    }
    public function setUserId(?int $userId): void
    {
        $this->userId = $userId;
    }
    public function getEmail(): ?string
    {
        return $this->email;
    }
    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }
    public function setPassword(?string $password): void
    {
        $this->password = $password;
    }

}