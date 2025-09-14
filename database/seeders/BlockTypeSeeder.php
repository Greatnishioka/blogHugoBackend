<?php

namespace Database\Seeders;

use App\Models\Articles\Blocks\BlockTypes;
use Illuminate\Database\Seeder;

class BlockTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        BlockTypes::create([
            'type_name' => 'paragraph',
            'description' => 'Paragraph block type for articles',
            'is_available' => true,
        ]);
    }
}
