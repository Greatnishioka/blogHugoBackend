<?php

namespace App\Domain\Articles\Entity\Blocks\Images;

use App\Domain\Articles\Entity\Blocks\BaseBlockEntity;
use App\Domain\Articles\Entity\Images\ImageEntity;

use JsonSerializable;

class ImageBlockEntity extends BaseBlockEntity implements JsonSerializable
{
    public function __construct(
        private BaseBlockEntity $baseBlockEntity,
        private ImageEntity $imageUrlEntity,
    ) {}

    public function jsonSerialize(): array
{
    return array_merge(
        $this->baseBlockEntity->jsonSerialize(),
        [
            'image_url' => $this->imageUrlEntity ? $this->imageUrlEntity->jsonSerialize() : null,
            'image_name' => $this->imageUrlEntity ? $this->imageUrlEntity->getImageName() : null,
            'alt_text' => $this->imageUrlEntity ? $this->imageUrlEntity->getAltText() : null,
        ]
    );
}

    public function getId(): ?int
    {
        return $this->baseBlockEntity->getId();
    }
    public function setId(?int $id): void
    {
        $this->baseBlockEntity->setId($id);
    }
    public function getArticleId(): ?int
    {
        return $this->baseBlockEntity->getArticleId();
    }
    public function setArticleId(?int $articleId): void
    {
        $this->baseBlockEntity->setArticleId($articleId);
    }
    public function getParentBlockUuid(): ?int
    {
        return $this->baseBlockEntity->getParentBlockUuid();
    }
    public function setParentBlockUuid(?int $parentBlockUuid): void
    {
        $this->baseBlockEntity->setParentBlockUuid($parentBlockUuid);
    }
    public function getBlockType(): ?string
    {
        return $this->baseBlockEntity->getBlockType();
    }
    
    public function setBlockType(?string $blockType): void
    {
        $this->baseBlockEntity->setBlockType($blockType);
    }
    
}