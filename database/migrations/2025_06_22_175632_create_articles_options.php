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
        Schema::create('articles_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained('articles')->onDelete('cascade')->comment('記事のID。articlesテーブルの外部キー。');
            $table->foreignId('option_id')->constrained('options')->onDelete('cascade')->comment('オプションのID。optionsテーブルの外部キー。');
            $table->boolean('option_value')->default(false)->comment('オプションの値。初期値は未適応のfalse。');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles_options');
    }
};
