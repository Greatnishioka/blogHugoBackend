<?php

namespace App\Domain\Articles\Entity\Images;

use JsonSerializable;

class ImageUrlEntity implements JsonSerializable
{
    public function __construct(
        private ?string $owner = null,
        private ?string $imageUrl = null,
        private ?string $imageName = null,
        private ?string $altText = null,
    ) {}

    public function jsonSerialize(): array
    {
        return [
            'owner' => $this->owner,
            'image_url' => $this->imageUrl,
            'image_name' => $this->imageName,
            'alt_text' => $this->altText,
        ];
    }
    public function getOwner(): ?string
    {
        return $this->owner;
    }
    public function setOwner(?string $owner): void
    {
        $this->owner = $owner;
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