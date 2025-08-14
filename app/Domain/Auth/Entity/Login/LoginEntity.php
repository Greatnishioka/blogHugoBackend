<?php

namespace App\Domain\Auth\Entity\Login;

use JsonSerializable;

class LoginEntity implements JsonSerializable
{
    public function __construct(
        private ?int $id,
        private ?string $userUuid,
    ) {}

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'userUuid' => $this->userUuid,
        ];
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    public function setId(?int $id): void
    {
        $this->id = $id;
    }
    public function getUserUuid(): ?string
    {
        return $this->userUuid;
    }
    public function setUserUuid(?string $userUuid): void
    {
        $this->userUuid = $userUuid;
    }
}