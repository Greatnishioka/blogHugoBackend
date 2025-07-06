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
        Schema::create('articles_tags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained('articles')->onDelete('cascade')->comment('記事のID。articlesテーブルの外部キー。');
            $table->foreignId('tag_id_1')->constrained('tags')->onDelete('cascade')->comment('タグのID1つ目。tagsテーブルの外部キー。');
            $table->foreignId('tag_id_2')->nullable()->constrained('tags')->onDelete('cascade')->comment('関連タグのID2つ目。tagsテーブルの外部キー。null許容。');
            $table->foreignId('tag_id_3')->nullable()->constrained('tags')->onDelete('cascade')->comment('追加の関連タグのID3つ目。tagsテーブルの外部キー。null許容。');
            $table->foreignId('tag_id_4')->nullable()->constrained('tags')->onDelete('cascade')->comment('追加の関連タグのID4つ目。tagsテーブルの外部キー。null許容。');
            $table->foreignId('tag_id_5')->nullable()->constrained('tags')->onDelete('cascade')->comment('追加の関連タグのID5つ目。tagsテーブルの外部キー。null許容。');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles_tags');
    }
};
