<?php

namespace App\Domain\Articles\Entity;

use JsonSerializable;

class ArticlesEntity implements JsonSerializable
{
    public function __construct(
        private ?int $id = null,
        private ?ArticleDetailEntity $detail = null,
        private ?ArticleTagsEntity $tags = null,
        private ?ArticleBlockEntity $blocks = null,
        private ?array $options = null,
    ) {}

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'detail' => $this->detail ? $this->detail->jsonSerialize() : null,
            'tags' => $this->tags ? $this->tags->jsonSerialize() : null,
            'blocks' => $this->blocks ? $this->blocks->jsonSerialize() : null,
            'options' => $this->options,
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
    public function setViewCount(?int $viewCount): void
    {
        $this->viewCount = $viewCount;
    }
    public function getBlocks(): ?ArticleBlockEntity
    {
        return $this->blocks;
    }
    public function getDetail ():ArticleDetailEntity
    {
        return $this->detail;
    }
    public function setDetail(?ArticleDetailEntity $detail): void
    {
        $this->detail = $detail;
    }
    public function setBlocks(?ArticleBlockEntity $blocks): void
    {
        $this->blocks = $blocks;
    }
    public function getTags(): ?ArticleTagsEntity
    {
        return $this->tags;
    }
    public function setTags(?ArticleTagsEntity $tags): void
    {
        $this->tags = $tags;
    }
    public function getOptions(): ?array
    {
        return $this->options;
    }
    public function setOptions(?array $options): void
    {
        $this->options = $options;
    }
}