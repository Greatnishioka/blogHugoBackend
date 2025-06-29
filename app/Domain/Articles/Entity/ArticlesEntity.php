<?php

namespace App\Domain\Articles\Entity;

use App\Domain\Articles\Entity\Blocks\ArticleBlockInfoEntity;
use App\Domain\Articles\Entity\Tags\ArticleTagsEntity;
use JsonSerializable;

class ArticlesEntity implements JsonSerializable
{
    public function __construct(
        private ?int $id = null,
        private ?string $articleId = null,
        private ?ArticleDetailEntity $detail = null,
        private ?ArticleTagsEntity $tags = null,
        private ?ArticleBlockInfoEntity $blocks = null,
        private ?ArticleOptionsEntity $options = null,
    ) {}

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'detail' => $this->detail ? $this->detail->jsonSerialize() : null,
            'blocks' => $this->blocks,
            'tags' => $this->tags,
            'options' => $this->options ? $this->options->jsonSerialize() : null,
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
    public function getArticleId(): ?string
    {
        return $this->articleId;
    }
    public function setArticleId(?string $articleId): void
    {
        $this->articleId = $articleId;
    }
    public function setViewCount(?int $viewCount): void
    {
        $this->viewCount = $viewCount;
    }
    public function getBlocks(): ?ArticleBlockInfoEntity
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
    public function setBlocks(?ArticleBlockInfoEntity $blocks): void
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
    public function getOptions(): ?ArticleOptionsEntity
    {
        return $this->options;
    }
    public function setOptions(?ArticleOptionsEntity $options): void
    {
        $this->options = $options;
    }
}