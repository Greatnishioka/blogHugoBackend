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
        // seederとかで挿入するのを想定
        Schema::create('block_types', function (Blueprint $table) {
            $table->id();
            $table->string('type_name', 48)->comment('ブロックのタイプ名。例: "text", "image", "video"など。');
            $table->string('description', 255)->nullable()->comment('ブロックの説明。');
            $table->boolean('is_available')->default(true)->comment('ブロックの利用可能状態。');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('block_types');
    }
};
