<?php

use Illuminate\Support\Facades\Route;

Route::post('/article', [\App\Http\Actions\RegisterArticles::class, 'handle']);
Route::post('/article/saveImages', [\App\Http\Actions\Articles\Images\ImagesSave::class, 'handle']);
Route::get('/article', [\App\Http\Actions\GetArticles::class, 'handle']);