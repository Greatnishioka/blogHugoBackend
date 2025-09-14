<?php
namespace App\Domain\Articles\DTO;

use App\Domain\Common\ValueObject\Uuid;

class GetArticleListDTO
{
    public Uuid $userId; // フロントエンドではuuidをユーザーIDとして使用している
    public int $perPage;

    public function __construct(string $userId, int $perPage)
    {
        $this->userId = Uuid::fromString($userId);
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