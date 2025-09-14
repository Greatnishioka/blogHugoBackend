<?php
namespace App\Domain\Articles\DTO;

use App\Domain\Common\ValueObject\Uuid;

class GetArticleDTO
{
    public string $userName;
    public Uuid $articleId;

    public function __construct(string $userName, string $articleId)
    {
        $this->userName = $userName;
        $this->articleId = Uuid::fromString($articleId);
    }

    public static function fromRequest($request): self
    {
        return new self(
            $request->query('userName'),
            $request->query('articleId')
        );
    }
}