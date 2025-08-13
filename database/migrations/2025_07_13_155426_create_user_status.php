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
        Schema::create('user_status', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->comment('記事のID。articlesテーブルの外部キー。');
            $table->foreignId('status_id')->constrained('status')->onDelete('cascade')->comment('ステータスのID。statusテーブルの外部キー。');
            $table->unsignedBigInteger('status_value')->default(0)->comment('ステータスの値。初期値は0。');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_status');
    }
};
