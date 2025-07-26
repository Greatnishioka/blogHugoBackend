<?php

namespace App\Domain\Articles\Entity\Images;

use JsonSerializable;

class ImagesEntity implements JsonSerializable
{
    public function __construct(
        /*
         * @param ImageEntity[] $images
         */
        private ?array $images = null,
    ) {
    }

    public function jsonSerialize(): array
    {
        return [
            'images' => $this->images,
        ];
    }

    public function getImages(): ?array
    {
        return $this->images;
    }
    public function setImages(?array $images): void
    {
        $this->images = $images;
    }
    public function __toString(): string
    {
        return json_encode($this->jsonSerialize());
    }
}