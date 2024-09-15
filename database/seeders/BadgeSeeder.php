<?php

namespace Database\Seeders;

use App\Models\Account;
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

        Badge::factory(50)->create();

        $account = Account::where('email', 'admin@pcbmc.co')->first();
        for ($i = 0; $i <= 15; $i++) {
            $account->badges()->attach(Badge::where('id', $i)->first());
        }
    }
}
