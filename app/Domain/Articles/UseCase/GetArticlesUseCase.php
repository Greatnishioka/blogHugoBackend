<?php

namespace App\Domain\Articles\UseCase;

use Illuminate\Http\Request;
use App\Exceptions\BaseException;
use App\Domain\Articles\Repository\ArticlesRepository;
use App\Domain\Articles\Entity\ArticlesEntity;

use RuntimeException;

class GetArticlesUseCase {
    private ArticlesRepository $repository;

    public function __construct(ArticlesRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(Request $request): ArticlesEntity
    {
        try{

            return $this->repository->getArticles($request);
            
        }catch(BaseException $e){
            throw new RuntimeException($e->getMessage());
        }
    }
}
