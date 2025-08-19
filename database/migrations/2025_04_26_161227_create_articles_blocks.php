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
        Schema::create('articles_blocks', function (Blueprint $table) {
            $table->id();
            $table->uuid('block_uuid')->unique()->comment('このブロックのUUID。ブロックのidを露出させたくないため、こちらを使用する。');
            $table->foreignId('article_id')->constrained('articles')->onDelete('cascade');
            $table->unsignedTinyInteger('order_from_parent_block')->nullable()->default(null)->comment('このブロックの順番。nullは未設定。');
            $table->foreignId('block_type_id')->constrained('block_types')->onDelete('cascade')->comment('ブロックのタイプ。block_typesテーブルの外部キー。');
            $table->text('content')->nullable()->comment('このブロックのコンテンツ。');
            $table->text('style')->nullable()->comment('基本空欄にしたい。もし手動でスタイルをいじる場合は、ここにCSSを入れる。');
            $table->timestamps();
        });

        Schema::table('articles_blocks', function (Blueprint $table) {
            $table->foreignUuid('parent_block_uuid')->nullable()->references('block_uuid')->on('articles_blocks')->onDelete('cascade')->comment('親ブロックのUUID。ulタグやaタグなどの構造化が避けられないタグに対して用いる。特に親要素がない場合はnull');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles_blocks');
    }
};
