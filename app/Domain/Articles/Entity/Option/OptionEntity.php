<?php

namespace App\Domain\Articles\Entity\Option;

use JsonSerializable;

class OptionEntity implements JsonSerializable
{
    public function __construct(
        private ?int $id = null,
        private ?string $optionName = null,
        private ?string $description = null,
    ) {}

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'option_name' => $this->optionName,
            'description' => $this->description,
        ];
    }
    public function getId(): ?int
    {
        return $this->id;
    }
    public function getOptionName(): ?string
    {
        return $this->optionName;
    }
    public function getDescription(): ?string
    {
        return $this->description;
    }
    public function setId(?int $id): void
    {
        $this->id = $id;
    }
    public function setOptionName(?string $optionName): void
    {
        $this->optionName = $optionName;
    }
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }
    
}