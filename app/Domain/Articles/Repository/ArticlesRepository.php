<?php

namespace App\Domain\Articles\Repository;
use App\Domain\Articles\Entity\ArticlesEntity;
use App\Domain\Articles\Entity\Images\ImagesEntity;
use App\Domain\Articles\DTO\RegisterArticleDTO;
use App\Domain\Articles\DTO\GetArticleListDTO;
use App\Domain\Articles\DTO\GetArticleDTO;
use App\Domain\Articles\DTO\UpdateArticleDTO;
use Illuminate\Http\Request;

interface ArticlesRepository {

    public function registerArticles(RegisterArticleDTO $dto): ArticlesEntity;
    public function getArticles(GetArticleDTO $dto): ArticlesEntity;
    public function updateArticles(UpdateArticleDTO $dto): ArticlesEntity;
    public function getArticlesList(GetArticleListDTO $dto): array;
    public function imageSave(Request $request): array; // 大規模になりそうだから引数の改善は後回し
    public function getInitProject(Request $request): ArticlesEntity;

}
