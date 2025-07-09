<?php

namespace App\Domain\Articles\Repository;
use App\Domain\Articles\Entity\ArticlesEntity;
use App\Domain\Articles\Entity\Images\ImagesEntity;
use Illuminate\Http\Request;

interface ArticlesRepository {

    public function registerArticles(Request $request):ArticlesEntity;
    public function getArticles(Request $request): ArticlesEntity;
    public function imageSave(Request $request): ImagesEntity;
    public function getInitProject(Request $request): ArticlesEntity;

}
