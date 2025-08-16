<?php

namespace App\Domain\Auth\Entity\Logout;

use JsonSerializable;

class LogoutEntity implements JsonSerializable
{
    public function __construct(
        private ?bool $success,
        private ?string $message
    ) {}

    public function jsonSerialize(): array
    {
        return [
            'success' => $this->success,
            'message' => $this->message,
        ];
    }

    public function getSuccess(): ?bool
    {
        return $this->success;
    }
    public function setSuccess(?bool $success): void
    {
        $this->success = $success;
    }
    public function getMessage(): ?string
    {
        return $this->message;
    }
    public function setMessage(?string $message): void
    {
        $this->message = $message;
    }
}