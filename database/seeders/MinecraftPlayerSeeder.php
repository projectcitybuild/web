<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\MinecraftPlayer;
use Illuminate\Database\Seeder;

class MinecraftPlayerSeeder extends Seeder
{
    public function run()
    {
        for ($i = 0; $i < 50; $i++) {
            MinecraftPlayer::factory()
                ->for(Account::factory(), 'account')
                ->create();
        }

        MinecraftPlayer::factory()->count(50)->create();
    }
}
