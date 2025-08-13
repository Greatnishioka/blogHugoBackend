<?php

namespace App\Domain\Articles\UseCase;

use Illuminate\Http\Request;

use App\Exceptions\BaseException;
use App\Domain\Articles\Repository\ArticlesRepository;
use App\Domain\Articles\Entity\ArticlesEntity;
use App\Domain\Articles\DTO\RegisterArticleDTO;

use RuntimeException;

class RegisterArticlesUseCase
{
    private ArticlesRepository $repository;

    public function __construct(ArticlesRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(Request $request): ArticlesEntity
    {
        try {
            $dto = RegisterArticleDTO::fromRequest($request);
            return $this->repository->registerArticles($dto);

        } catch (BaseException $e) {
            throw new RuntimeException($e->getMessage());
        }
    }
}
