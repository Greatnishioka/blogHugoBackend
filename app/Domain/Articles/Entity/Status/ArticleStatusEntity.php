<?php

namespace App\Domain\Articles\Entity;

use JsonSerializable;

class ArticleStatusEntity implements JsonSerializable
{
    public function __construct(
        private ?string $articleId = null,
        private ?string $viewCount = null,
    ) {}

    public function jsonSerialize(): array
    {
        return [
            'article_id' => $this->articleId,
            'view_count' => $this->viewCount,
        ];
    }

    public function getArticleId(): ?int
    {
        return $this->articleId;
    }
    public function setArticleId(?int $articleId): void
    {
        $this->articleId = $articleId;
    }
    public function getViewCount(): ?string
    {
        return $this->viewCount;
    }
    public function setViewCount(?string $viewCount): void
    {
        $this->viewCount = $viewCount;
    }
}