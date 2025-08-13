<?php

namespace App\Domain\Users\Entity\UserStatus;

use JsonSerializable;

class UserStatusEntity implements JsonSerializable
{
    public function __construct(
        private ?int $userId = null,
        private ?int $statusId = null,
        private ?bool $statusValue = null

    ) {}

    public function jsonSerialize(): array
    {
        return [
            'user_id' => $this->userId,
            'status_id' => $this->statusId,
            'status_value' => $this->statusValue,
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
    public function getStatusId(): ?int
    {
        return $this->statusId;
    }
    public function setStatusId(?int $statusId): void
    {
        $this->statusId = $statusId;
    }
    public function getStatusValue(): ?bool
    {
        return $this->statusValue;
    }
    public function setStatusValue(?bool $statusValue): void
    {
        $this->statusValue = $statusValue;
    }
}