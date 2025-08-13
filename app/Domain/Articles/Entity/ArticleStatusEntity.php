<?php

namespace App\Domain\Articles\Entity;

use JsonSerializable;

class ArticleStatusEntity implements JsonSerializable
{
    public function __construct(
        private ?int $articleId = null,
        private ?int $statusId = null,
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
    public function getArticleId(): ?int
    {
        return $this->articleId;
    }
    public function setArticleId(?int $articleId): void
    {
        $this->articleId = $articleId;
    }
    public function getStatusId(): ?int
    {
        return $this->statusId;
    }
    public function setStatusId(?int $statusId): void
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