<?php

namespace Database\Seeders;

use App\Models\Status\Status;
use App\Models\Options\Option;
use Illuminate\Database\Seeder;

class StatusAndOptionSeeder extends Seeder
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
    }
}
