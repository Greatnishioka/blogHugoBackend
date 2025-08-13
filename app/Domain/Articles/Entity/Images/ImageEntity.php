<?php

namespace App\Domain\Articles\Entity\Images;

use JsonSerializable;

class ImageEntity implements JsonSerializable
{
    public function __construct(
        private ?int $id = null,
        private ?string $blockUuid = null,
        private ?string $imageUrl = null,
        private ?string $imageName = null,
        private ?string $altText = null,
    ) {}

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'block_uuid' => $this->blockUuid,
            'image_url' => $this->imageUrl,
            'image_name' => $this->imageName,
            'alt_text' => $this->altText,
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
    public function getBlockUuid(): ?string
    {
        return $this->blockUuid;
    }
    public function setBlockUuid(?string $blockUuid): void
    {
        $this->blockUuid = $blockUuid;
    }
    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }
    public function setImageUrl(?string $imageUrl): void
    {
        $this->imageUrl = $imageUrl;
    }
    public function getImageName(): ?string
    {
        return $this->imageName;
    }
    public function setImageName(?string $imageName): void
    {
        $this->imageName = $imageName;
    }
    public function getAltText(): ?string
    {
        return $this->altText;
    }
    public function setAltText(?string $altText): void
    {
        $this->altText = $altText;
    }
    public function __toString(): string
    {
        return json_encode($this->jsonSerialize());
    }
    
}