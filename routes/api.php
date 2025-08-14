<?php

use Illuminate\Support\Facades\Route;

Route::post('/article', [\App\Http\Actions\Articles\Project\RegisterArticles::class, 'handle']);
Route::post('/article/saveImages', [\App\Http\Actions\Articles\Images\ImagesSave::class, 'handle']);
Route::post('/users/register', [\App\Http\Actions\Users\Register::class, 'handle']);
Route::get('/article', [\App\Http\Actions\GetArticles::class, 'handle']);
Route::get('/getInitProject', [\App\Http\Actions\Articles\Project\GetInitProject::class, 'handle']);
Route::post('/auth/login', [\App\Http\Actions\Auth\Login::class, 'handle']);