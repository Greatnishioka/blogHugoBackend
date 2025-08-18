<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('articles_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained('articles')->onDelete('cascade')->comment('記事のID。articlesテーブルの外部キー。');
            $table->foreignUuid('user_uuid')->references('user_uuid')->on('users')->onDelete('cascade')->comment('記事の作成者のユーザーID。usersテーブルの外部キー。');
            $table->string('title')->comment('記事のタイトル。');
            $table->text('description')->comment('記事の説明。');
            $table->string('note')->nullable()->comment('記事のメモ。');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles_detail');
    }
};
