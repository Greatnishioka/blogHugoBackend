<?php

namespace App\Domain\Users\Entity\UserData;

use JsonSerializable;

class UserDataEntity implements JsonSerializable
{
    public function __construct(
        private ?int $userId = null,
        private ?string $name = null,
        private ?string $iconUrl = null,
        private ?string $bio = null,
        private ?string $occupation = null,
        /*
         * @param UserStatusEntity[] $status
         */
        private ?array $status = null
    ) {
    }

    public function jsonSerialize(): array
    {
        return [
            'userId' => $this->userId,
            'name' => $this->name,
            'iconUrl' => $this->iconUrl,
            'bio' => $this->bio,
            'occupation' => $this->occupation,
            'status' => $this->status,
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
    public function getName(): ?string
    {
        return $this->name;
    }
    public function setName(?string $name): void
    {
        $this->name = $name;
    }
    public function getIconUrl(): ?string
    {
        return $this->iconUrl;
    }
    public function setIconUrl(?string $iconUrl): void
    {
        $this->iconUrl = $iconUrl;
    }
    public function getBio(): ?string
    {
        return $this->bio;
    }
    public function setBio(?string $bio): void
    {
        $this->bio = $bio;
    }
    public function getOccupation(): ?string
    {
        return $this->occupation;
    }
    public function setOccupation(?string $occupation): void
    {
        $this->occupation = $occupation;
    }
    public function getStatus(): ?array
    {
        return $this->status;
    }
    public function setStatus(?array $status): void
    {
        $this->status = $status;
    }

}