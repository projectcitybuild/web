<?php

namespace Database\Seeders;

use App\Models\Badge;
use Illuminate\Database\Seeder;

class BadgeSeeder extends Seeder
{
    public function run()
    {
        Badge::create([
            'display_name' => '2022 Event Winner',
            'unicode_icon' => '✦',
        ]);

        Badge::create([
            'display_name' => '10 years on PCB',
            'unicode_icon' => '✦',
        ]);
    }
}
