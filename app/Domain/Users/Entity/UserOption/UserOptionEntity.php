<?php

namespace App\Domain\Users\Entity\UsersOption;

use JsonSerializable;

class UserOptionEntity implements JsonSerializable
{
    public function __construct(
        private ?int $userId = null,
        private ?int $optionId = null,
        private ?bool $optionValue = null

    ) {
    }

    public function jsonSerialize(): array
    {
        return [
            'user_id' => $this->userId,
            'option_id' => $this->optionId,
            'option_value' => $this->optionValue,
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
    public function getOptionId(): ?int
    {
        return $this->optionId;
    }
    public function setOptionId(?int $optionId): void
    {
        $this->optionId = $optionId;
    }
    public function getOptionValue(): ?bool
    {
        return $this->optionValue;
    }
    public function setOptionValue(?bool $optionValue): void
    {
        $this->optionValue = $optionValue;
    }
}