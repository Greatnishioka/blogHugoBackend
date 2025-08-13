<?php

namespace App\Domain\Articles\Repository;
use App\Domain\Articles\Entity\ArticlesEntity;
use App\Domain\Articles\Entity\Images\ImagesEntity;
use App\Domain\Articles\DTO\RegisterArticleDTO;
use App\Domain\Articles\DTO\GetArticleDTO;
use Illuminate\Http\Request;

interface ArticlesRepository {

    public function registerArticles(RegisterArticleDTO $dto): ArticlesEntity;
    public function getArticles(Request $request): ArticlesEntity;
    public function updateArticles(Request $request): ArticlesEntity;
    public function getArticlesList(GetArticleDTO $dto): array;
    public function imageSave(Request $request): array;
    public function getInitProject(Request $request): ArticlesEntity;

}
