<?php

namespace App\Domain\Articles\Entity\Blocks;

use JsonSerializable;

class ArticleBlockInfoEntity implements JsonSerializable
{
    public function __construct(
        private ?int $articleId,
        /*
         * @param ArticlesBlockEntity[] $blocks
         */
        private ?array $blocks = null,
    ) {
    }

    public function jsonSerialize(): array
    {
        return [
            'article_id' => $this->articleId,
            'blocks' => $this->blocks ? array_map(fn($block) => $block->jsonSerialize(), $this->blocks) : [],
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
    public function getBlocks(): ?array
    {
        return $this->blocks;
    }
    public function setBlocks(?array $blocks): void
    {
        $this->blocks = $blocks;
    }
}