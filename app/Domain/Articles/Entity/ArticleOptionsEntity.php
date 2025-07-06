<?php

namespace App\Domain\Articles\Entity;

use JsonSerializable;

class ArticleOptionsEntity implements JsonSerializable
{
    public function __construct(
        private ?string $articleId = null,
        private ?string $optionId = null,
        private ?bool $optionValue = null

    ) {
    }

    public function jsonSerialize(): array
    {
        return [
            'article_id' => $this->articleId,
            'option_id' => $this->optionId,
            'option_value' => $this->optionValue,
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
    public function getOptionId(): ?string
    {
        return $this->optionId;
    }
    public function setOptionId(?string $optionId): void
    {
        $this->optionId = $optionId;
    }
    public function getOptionValue(): ?bool
    {
        return $this->optionValue;
    }
    public function setOptionValue(?bool $optionValue): void
    {
        $this->optionValue = $optionValue;
    }
}