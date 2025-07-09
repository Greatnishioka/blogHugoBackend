<?php

namespace App\Domain\Articles\Entity;

use JsonSerializable;

use App\Domain\Articles\Entity\Images\ImageUrlEntity;

class ArticleDetailEntity implements JsonSerializable
{
    public function __construct(
        private ?string $articleId = null,
        private ?string $title = null,
        private ?string $author = null,
        private ?int $authorId = null,
        private ?ImageUrlEntity $topImage = null,
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
            'author' => $this->author,
            'author_id' => $this->authorId,
            'top_image' => $this->topImage ? $this->topImage->jsonSerialize() : null,
            'status' => $this->status ? array_map(fn($s) => $s->jsonSerialize(), $this->status) : null,
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
    public function getTitle(): ?string
    {
        return $this->title;
    }
    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }
    public function getAuthor(): ?string
    {
        return $this->author;
    }
    public function setAuthor(?string $author): void
    {
        $this->author = $author;
    }
    public function getAuthorId(): ?int
    {
        return $this->authorId;
    }
    public function setAuthorId(?int $authorId): void
    {
        $this->authorId = $authorId;
    }
    public function getTopImage(): ?ImageUrlEntity
    {
        return $this->topImage;
    }
    public function setTopImage(?ImageUrlEntity $topImage): void
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