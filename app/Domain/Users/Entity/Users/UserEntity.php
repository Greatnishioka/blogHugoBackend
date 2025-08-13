<?php

namespace App\Domain\Users\Entity\Users;

use App\Domain\Users\Entity\UserData\UserDataEntity;
use App\Domain\Users\Entity\UsersOption\UserOptionEntity;
use App\Domain\Users\Entity\UsersStatus\UserStatusEntity;
use JsonSerializable;

class UserEntity implements JsonSerializable
{
    public function __construct(
        private ?int $id = null,
        private ?string $userUuid = null,
        private ?UserDataEntity $userData = null,
        private ?array $userOption = null,
        private ?array $userStatus = null,
    ) {
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'userUuid' => $this->userUuid,
            'userData' => $this->userData ? $this->userData->jsonSerialize() : null,
            'userOption' => $this->userOption ? array_map(fn($option) => $option->jsonSerialize(), $this->userOption) : null,
            'userStatus' => $this->userStatus ? array_map(fn($status) => $status->jsonSerialize(), $this->userStatus) : null,
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
    public function getUserData(): ?UserDataEntity
    {
        return $this->userData;
    }
    public function setUserData(?UserDataEntity $userData): void
    {
        $this->userData = $userData;
    }
    public function getUserOption(): ?array
    {
        return $this->userOption;
    }
    public function setUserOption(?array $userOption): void
    {
        $this->userOption = $userOption;
    }
    public function getUserStatus(): ?array
    {
        return $this->userStatus;
    }
    public function setUserStatus(?array $userStatus): void
    {
        $this->userStatus = $userStatus;
    }

}