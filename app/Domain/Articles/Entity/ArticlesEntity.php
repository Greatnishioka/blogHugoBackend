<?php

namespace App\Domain\Articles\Entity;

use JsonSerializable;

class ArticlesEntity implements JsonSerializable
{
    public function __construct(
        private ?int $id,
        private ?string $title,
        private ?string $author,
        private ?int $authorId,
        private ?string $viewCount,
        /*
         * @param ArticlesBlockEntity[] $blocks
         */
        private ?Array $blocks,
    ) {}

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'author' => $this->author,
            'author_id' => $this->authorId,
            'view_count' => $this->viewCount,
            'blocks' => $this->blocks,
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
    public function getViewCount(): ?int
    {
        return $this->viewCount;
    }
    public function setViewCount(?int $viewCount): void
    {
        $this->viewCount = $viewCount;
    }
    public function getBlocks(): ?array
    {
        return $this->blocks;
    }
    public function setBlocks(?array $blocks): void
    {
        $this->blocks = $blocks;
    }
}