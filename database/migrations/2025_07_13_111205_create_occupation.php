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
        Schema::create('occupations', function (Blueprint $table) {
            $table->id();
            $table->string('occupation_name', 48)->comment('職業名。例: "student", "engineer", "designer"など。');
            $table->string('occupation_name_ja', 48)->comment('職業名。例: "学生", "エンジニア", "デザイナー"など。');
            $table->string('description', 255)->nullable()->comment('職業の説明。');
            $table->boolean('is_available')->default(true)->comment('職業の利用可能状態。');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('occupations');
    }
};
