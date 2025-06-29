<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('articles_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained('articles')->onDelete('cascade')->comment('記事のID。articlesテーブルの外部キー。');
            $table->string('title')->comment('記事のタイトル。');
            $table->string('author')->comment('記事の著者名。');
            $table->unsignedBigInteger('author_id')->comment('記事の著者ID。usersテーブルの外部キー。');
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
