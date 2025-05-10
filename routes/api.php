<?php

use Illuminate\Support\Facades\Route;

Route::post('/register', [\App\Http\Actions\RegisterArticles::class, 'handle']);
Route::get('/getArticle', [\App\Http\Actions\GetArticles::class, 'handle']);