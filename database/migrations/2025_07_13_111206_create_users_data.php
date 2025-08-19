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
        Schema::create('users_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->comment('記事の作成者のユーザーID。usersテーブルの外部キー。');
            $table->string('name', 36)->comment('ユーザーの名前。');
            $table->string('icon_url')->nullable()->comment('ユーザーのアイコンURL。');
            $table->text('bio')->comment('ユーザーの自己紹介。');
            $table->foreignId('occupation_id')->constrained('occupations')->onDelete('cascade')->comment('職業のID。occupationsテーブルの外部キー。');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_data');
    }
};
