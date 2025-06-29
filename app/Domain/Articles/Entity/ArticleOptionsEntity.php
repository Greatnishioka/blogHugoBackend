<?php

namespace App\Domain\Articles\Entity;

use JsonSerializable;

class ArticleOptionsEntity implements JsonSerializable
{
    public function __construct(
        private ?string $articleId = null,
        private ?string $isPrivate = null,
    ) {
    }

    public function jsonSerialize(): array
    {
        return [
            'article_id' => $this->articleId,
            'isPrivate' => $this->isPrivate,
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

    public function getIsPrivate(): ?string
    {
        return $this->isPrivate;
    }
    public function setIsPrivate(?string $isPrivate): void
    {
        $this->isPrivate = $isPrivate;
    }

}