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
        Schema::create('articles_blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained('articles')->onDelete('cascade');
            $table->unsignedInteger('parent_block_id')->nullable()->comment('親ブロックのID。ulタグやaタグなどの構造化が避けられないタグに対して用いる。特に親要素がない場合はnull');
            $table->enum(
                'block_type', 
                [
                    'headding1', 'headding2', 'headding3', 'headding4', 'headding5','headding6'
                    ]
                    )->comment('このブロックの種類。多分種類増える');
            $table->text('content')->nullable()->comment('このブロックのコンテンツ。imgタグなど限定的な場合にのみこのcontentが空になる。');
            $table->text('style')->nullable()->comment('基本空欄にしたい。もし手動でスタイルをいじる場合は、ここにCSSを入れる。');
            $table->string('url')->nullable()->comment('このブロックの中で使うURL。画像や動画、遷移先などのURL');
            $table->string('language')->nullable()->comment('このブロックの記述言語。HTMLの場合は空欄');
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
