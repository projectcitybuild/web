<?php

namespace Database\Seeders;

use Entities\Models\Eloquent\Badge;
use Illuminate\Database\Seeder;

class BadgeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
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
