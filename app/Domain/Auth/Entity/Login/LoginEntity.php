<?php

namespace App\Domain\Users\Entity\Login;

use JsonSerializable;

class LoginEntity implements JsonSerializable
{
    public function __construct(
        private ?int $id,
        private ?int $articleId,
        private ?int $parentBlockUuid,
        private ?string $blockType,
        private ?string $content,
        private ?string $style,
        private ?string $url,
        private ?string $language,
    ) {}

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'article_id' => $this->articleId,
            'parent_block_uuid' => $this->parentBlockUuid,
            'block_type' => $this->blockType,
            'content' => $this->content,
            'style' => $this->style,
            'url' => $this->url,
            'language' => $this->language,
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
    public function getParentBlockUuid(): ?int
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
    public function getUrl(): ?string
    {
        return $this->url;
    }
    public function setUrl(?string $url): void
    {
        $this->url = $url;
    }
    public function getLanguage(): ?string
    {
        return $this->language;
    }
    public function setLanguage(?string $language): void
    {
        $this->language = $language;
    }
}