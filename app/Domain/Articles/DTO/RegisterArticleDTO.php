<?php
namespace App\Domain\Articles\DTO;

class RegisterArticleDTO
{
    public array $blocks;
    public array $detail;
    public array $status;
    public array $tags;
    public array $options;

    public function __construct(array $blocks, array $detail, array $status, array $tags, array $options)
    {
        $this->blocks = $blocks;
        $this->detail = $detail;
        $this->status = $status;
        $this->tags = $tags;
        $this->options = $options;
    }

    public static function fromRequest($request): self
    {
        return new self(
            $request->input('blocks', []),
            $request->input('detail', []),
            $request->input('status', []),
            $request->input('tags', []),
            $request->input('options', [])
        );
    }
}