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
            $table->string('parent_block_uuid')->nullable()->comment('親ブロックのUUID。ulタグやaタグなどの構造化が避けられないタグに対して用いる。特に親要素がない場合はnull');
            $table->unsignedInteger('order_from_parent_block')->nullable()->default(null)->comment('このブロックの順番。nullは未設定。');
            $table->enum(
                'block_type',
                [
                    'heading1',
                    'heading2',
                    'heading3',
                    'heading4',
                    'heading5',
                    'heading6',
                    'paragraph',
                    'image',
                    'ul',
                    'list',
                    'link',
                    'code',
                    'img',
                ]
            )->comment('このブロックの種類。多分種類増える');
            $table->text('content')->nullable()->comment('このブロックのコンテンツ。');
            $table->text('style')->nullable()->comment('基本空欄にしたい。もし手動でスタイルをいじる場合は、ここにCSSを入れる。');
            $table->timestamps();
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
