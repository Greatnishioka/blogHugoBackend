<?php

use Illuminate\Support\Facades\Route;

// 認証関連のルート
Route::post('/auth/login', [\App\Http\Actions\Auth\Login::class, 'handle']);
Route::post('/auth/logout', [\App\Http\Actions\Auth\Logout::class, 'handle']);

// 記事関連のルート
Route::get('/article', [\App\Http\Actions\Articles\Project\GetArticles::class, 'handle']);
Route::post('/article', [\App\Http\Actions\Articles\Project\RegisterArticles::class, 'handle']);
// Route::put('/article', [\App\Http\Actions\Articles\Project\RegisterArticles::class, 'handle']);
// Route::delete('/article', [\App\Http\Actions\Articles\Project\RegisterArticles::class, 'handle']);
// Route::get('/article/list', [\App\Http\Actions\Articles\Project\GetArticles::class, 'handle']);
Route::post('/article/saveImages', [\App\Http\Actions\Articles\Images\ImagesSave::class, 'handle']);

// ユーザー関連のルート
Route::post('/users/register', [\App\Http\Actions\Users\Register::class, 'handle']);
Route::get('/getInitProject', [\App\Http\Actions\Articles\Project\GetInitProject::class, 'handle']);

// 一旦ログイン機能がうまく動かないので、開発中のみ全てのルートを認証なしでアクセス可能にする
Route::middleware('auth:sanctum')->group(function () {
});