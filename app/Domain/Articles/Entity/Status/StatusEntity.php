<?php

namespace App\Domain\Articles\Entity\Status;

use JsonSerializable;

class StatusEntity implements JsonSerializable
{
    public function __construct(
        private ?int $id = null,
        private ?string $statusName = null,
        private ?string $description = null,
    ) {}

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'status_name' => $this->statusName,
            'description' => $this->description,
        ];
    }
    public function getId(): ?int
    {
        return $this->id;
    }
    public function getStatusName(): ?string
    {
        return $this->statusName;
    }
    public function getDescription(): ?string
    {
        return $this->description;
    }
    public function setId(?int $id): void
    {
        $this->id = $id;
    }
    public function setStatusName(?string $statusName): void
    {
        $this->statusName = $statusName;
    }
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }
    
}