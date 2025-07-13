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
        Schema::create('users_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->comment('記事の作成者のユーザーID。usersテーブルの外部キー。');
            $table->string('name')->comment('ユーザーの名前。');
            $table->string('icon_url')->nullable()->comment('ユーザーのアイコンURL。');
            $table->string('bio')->comment('ユーザーの自己紹介。');
            $table->enum('occupation', ['student', 'engineer', 'designer', 'manager', 'other'])->default('other')->comment('職業。');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_detail');
    }
};
