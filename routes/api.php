<?php

use Illuminate\Support\Facades\Route;

Route::post('/article', [\App\Http\Actions\RegisterArticles::class, 'handle']);
Route::get('/article', [\App\Http\Actions\GetArticles::class, 'handle']);