<?php

namespace App\Domain\Articles\Entity;

use JsonSerializable;
// VO
use App\Domain\Articles\ValueObject\ArticleUuid;

// Entities
use App\Domain\Articles\Entity\Blocks\BlockEntity;

class ArticlesEntity implements JsonSerializable
{
    public function __construct(
        private ?int $id = null,
        private ?ArticleUuid $articleUuid = null,
        private ?ArticleDetailEntity $detail = null,
        private ?ArticleTagsEntity $tags = null,
        private ?ArticleBlockEntity $blocks = null,
        /*
         * @param ArticlesStatusEntity[] $options
         */
        private ?array $options = null,
    ) {
    }

    // ----------------
    // Factories
    // ----------------
    public static function reconstitute(
        ?int $id,
        ?string $articleUuid,
        ?ArticleDetailEntity $detail,
        ?ArticleTagsEntity $tags,
        ?ArticleBlockEntity $blocks,
        ?array $options
    ): self {
        return new self(
            $id,
            $articleUuid ? ArticleUuid::fromString($articleUuid) : null,
            $detail,
            $tags,
            $blocks,
            $options
        );
    }

    public static function createDraft(?string $articleUuid = null): self
    {
        return new self(
            null,
            $articleUuid ? ArticleUuid::fromString($articleUuid) : ArticleUuid::generate(),
            null,
            new ArticleTagsEntity(null, []),
            new ArticleBlockEntity(null, []),
            []
        );
    }

    // ----------------
    // ドメインの振る舞い
    // ----------------

    public function sortBlockForRegister(array $blockAttributes): void
    {

    }

    public function addTag(array $tagAttributes): void
    {
        if ($this->tags === null) {
            $this->tags = new ArticleTagsEntity($this->id, []);
        }

        $tags = $this->tags->getTags() ?? [];

        foreach ($tags as $t) {
            if (
                is_array($t) &&
                (isset($t['id']) &&
                    isset($tagAttributes['id']) &&
                    $t['id'] === $tagAttributes['id'])
            ) {
                return;
            }
            if (
                is_array($t) &&
                isset($t['content']) &&
                isset($tagAttributes['content']) &&
                $t['content'] === $tagAttributes['content']
            ) {
                return;
            }
        }

        $tags[] = $tagAttributes;
        $this->tags->setTags($tags);
    }

    public function removeTagById(int $tagId): void
    {
        if ($this->tags === null) {
            return;
        }

        $tags = $this->tags->getTags() ?? [];
        $filtered = array_values(array_filter($tags, fn($t) => !(is_array($t) && isset($t['id']) && $t['id'] === $tagId)));
        $this->tags->setTags($filtered);
    }

    public function addBlock(BlockEntity $block): void
    {
        if ($this->blocks === null) {
            $this->blocks = new ArticleBlockEntity($this->id, []);
        }
        $blocks = $this->blocks->getBlocks() ?? [];
        $blocks[] = $block;
        $this->blocks->setBlocks($blocks);
    }

    public function replaceBlocks(array $blocks): void
    {
        if ($this->blocks === null) {
            $this->blocks = new ArticleBlockEntity($this->id, []);
        }
        // Expecting array of BlockEntity or arrays convertible by BlockEntity
        $this->blocks->setBlocks($blocks);
    }

    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    public function setStatuses(array $statuses): void
    {
        if ($this->detail === null) {
            $this->detail = new ArticleDetailEntity(null, null, null, null, $statuses);
            return;
        }
        $this->detail->setStatus($statuses);
    }

    public function changeTitle(string $title): void
    {
        if ($this->detail === null) {
            $this->detail = ArticleDetailEntity::createDraft();
        }
        $this->detail->setTitle($title);
    }

    public function changeNote(?string $note): void
    {
        if ($this->detail === null) {
            $this->detail = ArticleDetailEntity::createDraft();
        }
        $this->detail->setNote($note);
    }

    // どう言う実装か不明
    // public function publish(): void
    // {
    //     if (!$this->canPublish()) {
    //         throw new \LogicException('記事は公開できる状態ではありません。必要な情報を満たしているか確認してください。');
    //     }

    //     // set option publish flag if exists
    //     $options = $this->options ?? [];
    //     $found = false;
    //     foreach ($options as &$op) {
    //         if (isset($op['optionId']) && ($op['optionId'] === 'publish' || $op['optionName'] === 'publish')) {
    //             $op['optionValue'] = true;
    //             $found = true;
    //             break;
    //         }
    //     }
    //     if (!$found) {
    //         $options[] = [
    //             'option_id' => 'publish',
    //             'option_value' => true
    //         ];
    //     }

    //     $this->options = $options;
    // }

    // public function unpublish(): void
    // {
    //     $options = $this->options ?? [];
    //     foreach ($options as &$op) {
    //         if (isset($op['option_id']) && ($op['option_id'] === 'publish' || $op['option_name'] === 'publish')) {
    //             $op['option_value'] = false;
    //         }
    //     }
    //     $this->options = $options;
    // }

    // 必要なのか？
    public function canPublish(): bool
    {
        // Basic rules: title exists and at least one status is set
        $title = $this->detail?->getTitle();
        $statuses = $this->detail?->getStatus();

        if (empty($title)) {
            return false;
        }

        if (empty($statuses) || !is_array($statuses) || count($statuses) === 0) {
            return false;
        }

        return true;
    }

    // ----------------
    // Accessors
    // ----------------
    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
        // propagate to child holders
        if ($this->tags !== null) {
            $this->tags->setArticleId($id);
        }
        if ($this->blocks !== null) {
            $this->blocks->setArticleId($id);
        }
    }

    public function getArticleUuid(): ?string
    {
        return $this->articleUuid?->toString();
    }

    public function setArticleUuid(?string $articleUuid): void
    {
        $uuid = $articleUuid ? ArticleUuid::fromString($articleUuid) : null;
        $this->articleUuid = $uuid;
    }

    public function getBlocks(): ?ArticleBlockEntity
    {
        return $this->blocks;
    }

    public function getDetail(): ?ArticleDetailEntity
    {
        return $this->detail;
    }

    public function setDetail(?ArticleDetailEntity $detail): void
    {
        $this->detail = $detail;
    }

    public function setBlocks(?ArticleBlockEntity $blocks): void
    {
        $this->blocks = $blocks;
    }

    public function getTags(): ?ArticleTagsEntity
    {
        return $this->tags;
    }

    public function setTags(?ArticleTagsEntity $tags): void
    {
        $this->tags = $tags;
    }

    public function getOptions(): ?array
    {
        return $this->options;
    }

    public function setOptionsRaw(?array $options): void
    {
        $this->options = $options;
    }

    // ----------------
    // Serialization
    // ----------------
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'article_uuid' => $this->articleUuid?->toString(),
            'detail' => $this->detail ? $this->detail->jsonSerialize() : null,
            'tags' => $this->tags ? $this->tags->jsonSerialize() : null,
            'blocks' => $this->blocks ? $this->blocks->jsonSerialize() : null,
            'options' => $this->options,
        ];
    }
}