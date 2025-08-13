<?php

namespace App\Domain\Articles\Entity;

use JsonSerializable;

use App\Domain\Articles\Entity\Images\ImageEntity;

class ArticleDetailEntity implements JsonSerializable
{
    public function __construct(
        private ?string $articleId = null,
        private ?string $title = null,
        private ?string $note = null,
        private ?ImageEntity $topImage = null,
        /*
         * @param ArticleStatusEntity[] $status
         */
        private ?array $status = null,
    ) {
    }

    public function jsonSerialize(): array
    {
        return [
            'article_id' => $this->articleId,
            'title' => $this->title,
            'note' => $this->note,
            'top_image' => $this->topImage ? $this->topImage->jsonSerialize() : null,
            'status' => $this->status ? array_map(fn($s) => $s->jsonSerialize(), $this->status) : null,
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
    public function getTitle(): ?string
    {
        return $this->title;
    }
    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }
    public function getNote(): ?string
    {
        return $this->note;
    }
    public function setNote(?string $note): void
    {
        $this->note = $note;
    }
    public function getTopImage(): ?ImageEntity
    {
        return $this->topImage;
    }
    public function setTopImage(?ImageEntity $topImage): void
    {
        $this->topImage = $topImage;
    }
    public function getStatus(): ?array
    {
        return $this->status;
    }
    public function setStatus(?array $status): void
    {
        $this->status = $status;
    }

}