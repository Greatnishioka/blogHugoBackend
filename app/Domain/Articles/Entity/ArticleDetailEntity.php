<?php

namespace App\Domain\Articles\Entity;

use JsonSerializable;

use App\Domain\Articles\ValueObject\ArticleDetail\ArticleTitle;
use App\Domain\Articles\ValueObject\ArticleDetail\ArticleNote;
use App\Domain\Articles\ValueObject\ArticleDetail\ArticleDescription;

use App\Domain\Articles\Entity\Images\ImageEntity;

class ArticleDetailEntity implements JsonSerializable
{
    public function __construct(
        private ?ArticleTitle $title = null,
        private ?ArticleDescription $description = null,
        private ?ArticleNote $note = null,
        private ?ImageEntity $topImage = null,
        /*
         * @param ArticleStatusEntity[] $status
         */
        private ?array $status = null,
    ) {
    }

    // ----------------
    // Factories
    // ----------------
    public static function reconstitute(
        ?string $title,
        ?string $description,
        ?string $note,
        ?ImageEntity $topImage,
        ?array $status
    ): self {
        return new self(
            $title ? ArticleTitle::fromString($title) : null,
            $description ? ArticleDescription::fromString($description) : null,
            $note ? ArticleNote::fromString($note) : null,
            $topImage,
            $status
        );
    }

    public static function createDraft(): self
    {
        return new self(
            null,
            null,
            null,
            new ImageEntity(null, ""),
            []
        );
    }

    // ----------------
    // ドメインの振る舞い
    // ----------------

    public function jsonSerialize(): array
    {
        return [
            'title' => $this->title ? $this->title->toString() : null,
            'description' => $this->description ? $this->description->toString() : null,
            'note' => $this->note,
            'top_image' => $this->topImage ? $this->topImage->jsonSerialize() : null,
            'status' => $this->status ? array_map(fn($s) => $s->jsonSerialize(), $this->status) : null,
        ];
    }

    public function getTitle(): ?string
    {
        return $this->title?->toString();
    }
    public function setTitle(?string $title): void
    {
        $this->title = $title ? ArticleTitle::fromString($title) : null;
    }

    public function getDescription(): ?string
    {
        return $this->description?->toString();
    }
    public function setDescription(?string $description): void
    {
        $this->description = $description ? ArticleDescription::fromString($description) : null;
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