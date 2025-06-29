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
        // 現在はview_countだけですが、将来的に記事のステータスを管理するためのテーブルとして準備。
        Schema::create('articles_status', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained('articles')->onDelete('cascade')->comment('記事のID。articlesテーブルの外部キー。');
            $table->unsignedInteger('view_count')->default(0)->comment('記事の閲覧数。初期値は0。');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles_status');
    }
};
