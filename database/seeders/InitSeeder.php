<?php

namespace Database\Seeders;

use App\Models\Status\Status;
use App\Models\Options\Option;
use App\Models\Articles\Blocks\BlockTypes;
use App\Models\User\Occupations\Occupations;

use Illuminate\Database\Seeder;

class InitSeeder extends Seeder
{

    public function run(): void
    {
        Status::create([
            'status_name' => 'test',
            'description' => 'Draft status for articles',
        ]);

        Option::create([
            'option_name' => 'draft',
            'description' => 'Default status for new articles',
        ]);

        BlockTypes::create([
            'type_name' => 'img',
            'description' => 'Image block type for articles',
            'is_available' => true,
        ]);

        Occupations::create([
            'occupation_name' => 'engineer',
            'occupation_name_ja' => 'エンジニア',
            'description' => 'Responsible for developing and maintaining software applications.',
            'is_available' => true,
        ]);
    }
}
