<?php
namespace App\Domain\Articles\DTO;

class GetArticleDTO
{
    public string $userId; // フロントエンドではuuidをユーザーIDとして使用している
    public int $perPage;

    public function __construct(string $userId, int $perPage)
    {
        $this->userId = $userId;
        $this->perPage = $perPage;
    }

    public static function fromRequest($request): self
    {
        return new self(
            $request->query('userId'),
            $request->query('perPage', 10)
        );
    }
}