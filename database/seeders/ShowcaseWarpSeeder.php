<?php

namespace Database\Seeders;

use Entities\Models\Eloquent\ShowcaseWarp;
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
