<?php
namespace App\Domain\Articles\DTO;

use App\Domain\Common\ValueObject\Uuid;

class UpdateArticleDTO
{
    public Uuid $articleUuid;
    public array $detail;
    public array $blocks;
    public array $status;
    public array $tags;
    public array $options;

    public function __construct(
        string $articleUuid,
        array $detail,
        array $blocks,
        array $status,
        array $tags,
        array $options
    ) {
        $this->articleUuid = Uuid::fromString($articleUuid);
        $this->detail = $detail;
        $this->blocks = $blocks;
        $this->status = $status;
        $this->tags = $tags;
        $this->options = $options;
    }

    public static function fromRequest($request): self
    {

        return new self(
            $request->input('articleUuid'),
            $request->input('blocks', []),
            $request->input('detail', []),
            $request->input('status', []),
            $request->input('tags', []),
            $request->input('options', [])
        );
    }
}