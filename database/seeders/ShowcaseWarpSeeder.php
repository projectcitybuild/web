<?php

namespace Database\Seeders;

use App\Models\ShowcaseWarp;
use Illuminate\Database\Seeder;

class ShowcaseWarpSeeder extends Seeder
{
    public function run()
    {
        ShowcaseWarp::factory()
            ->count(75)
            ->create();
    }
}
