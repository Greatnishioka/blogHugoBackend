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
        Schema::table('articles_blocks', function (Blueprint $table) {
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
                    'new_type1',
                    'new_type2',
                ]
            )->comment('このブロックの種類。多分種類増える')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles_blocks', function (Blueprint $table) {
            //
        });
    }
};
