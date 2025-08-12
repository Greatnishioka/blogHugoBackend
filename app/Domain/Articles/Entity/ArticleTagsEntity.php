<?php

namespace App\Domain\Articles\Entity;

use JsonSerializable;

class ArticleTagsEntity implements JsonSerializable
{
    public function __construct(
        private ?string $articleId = null,
        private ?array $tags = null,
    ) {}

    public function jsonSerialize(): array
    {
        return [
            'article_id' => $this->articleId,
            'tags' => $this->tags,
        ];
    }

    public function getArticleId(): ?string
    {
        return $this->articleId;
    }
    public function setArticleId(?string $articleId): void
    {
        $this->articleId = $articleId;
    }
    public function getTags(): ?array
    {
        return $this->tags;
    }
    public function setTags(?array $tags): void
    {
        $this->tags = $tags;
    }
    
}