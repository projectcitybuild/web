<?php

namespace Database\Seeders;

use App\Entities\Models\Eloquent\Account;
use App\Entities\Models\Eloquent\GameBan;
use App\Entities\Models\Eloquent\MinecraftPlayer;
use Illuminate\Database\Seeder;

class GameBanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        GameBan::factory()
            ->for(MinecraftPlayer::factory()->for(Account::factory()), 'bannedPlayer')
            ->for(MinecraftPlayer::factory()->for(Account::factory()), 'staffPlayer')
            ->count(30)
            ->create();
    }
}
