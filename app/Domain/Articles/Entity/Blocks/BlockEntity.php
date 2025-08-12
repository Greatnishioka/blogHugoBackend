<?php

namespace App\Domain\Articles\Entity\Blocks;

use JsonSerializable;

class BlockEntity implements JsonSerializable
{
    public function __construct(
        private ?int $id,
        private ?string $blockUuid,
        private ?int $articleId,
        private ?string $parentBlockUuid,
        private ?string $blockType,
        private ?string $content,
        private ?string $style,
        private ?array $etc,
    ) {}

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'block_uuid' => $this->blockUuid,
            'article_id' => $this->articleId,
            'parent_block_uuid' => $this->parentBlockUuid,
            'block_type' => $this->blockType,
            'content' => $this->content,
            'style' => $this->style,
            'etc' => $this->etc,
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
    public function getArticleId(): ?int
    {
        return $this->articleId;
    }
    public function setArticleId(?int $articleId): void
    {
        $this->articleId = $articleId;
    }
    public function getBlockUuid(): ?string
    {
        return $this->blockUuid;
    }
    public function setBlockUuid(?string $blockUuid): void
    {
        $this->blockUuid = $blockUuid;
    }
    public function getParentBlockUuid(): ?string
    {
        return $this->parentBlockUuid;
    }
    public function setParentBlockId(?int $parentBlockId): void
    {
        $this->parentBlockId = $parentBlockId;
    }
    public function getBlockType(): ?string
    {
        return $this->blockType;
    }
    public function setBlockType(?string $blockType): void
    {
        $this->blockType = $blockType;
    }
    public function getContent(): ?string
    {
        return $this->content;
    }
    public function setContent(?string $content): void
    {
        $this->content = $content;
    }
    public function getStyle(): ?string
    {
        return $this->style;
    }
    public function setStyle(?string $style): void
    {
        $this->style = $style;
    }
    public function getEtc(): ?array
    {
        return $this->etc;
    }
    public function setEtc(?array $etc): void
    {
        $this->etc = $etc;
    }
}