<?php

namespace App\Domain\Articles\Entity;

use JsonSerializable;

class ArticleStatusEntity implements JsonSerializable
{
    public function __construct(
        private ?string $articleId = null,
        private ?string $statusId = null,
        private ?bool $statusValue = null

    ) {
    }

    public function jsonSerialize(): array
    {
        return [
            'article_id' => $this->articleId,
            'status_id' => $this->statusId,
            'status_value' => $this->statusValue,
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
    public function getStatusId(): ?string
    {
        return $this->statusId;
    }
    public function setStatusId(?string $statusId): void
    {
        $this->statusId = $statusId;
    }
    public function getStatusValue(): ?bool
    {
        return $this->statusValue;
    }
    public function setStatusValue(?bool $statusValue): void
    {
        $this->statusValue = $statusValue;
    }
}