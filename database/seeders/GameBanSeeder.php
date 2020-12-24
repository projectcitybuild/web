<?php

namespace Database\Seeders;

use App\Entities\Accounts\Models\Account;
use App\Entities\Bans\Models\GameBan;
use App\Entities\Players\Models\MinecraftPlayer;
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
