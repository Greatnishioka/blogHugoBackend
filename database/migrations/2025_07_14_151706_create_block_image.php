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
        Schema::create('block_image', function (Blueprint $table) {
            $table->id();
            $table->string('block_uuid')->comment('ブロックのUUID。これだけは外部に露出するのでUUIDを使用する。');
            $table->string('image_url')->nullable()->comment('画像のURL。');
            $table->string('image_name')->nullable()->comment('画像の名前。');
            $table->string('alt_text')->nullable()->comment('画像の代替テキスト。');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('block_image');
    }
};
