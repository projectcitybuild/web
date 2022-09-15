<?php

namespace Database\Seeders;

use Entities\Models\Eloquent\Account;
use Entities\Models\Eloquent\GamePlayerBan;
use Entities\Models\Eloquent\MinecraftPlayer;
use Illuminate\Database\Seeder;

class GamePlayerBanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $staffPlayers = collect([
            MinecraftPlayer::factory()->create(),
            MinecraftPlayer::factory()->for(Account::factory())->create(),
            null,
        ]);

        $players = MinecraftPlayer::get();

        for ($i = 0; $i < 100; $i++) {
            GamePlayerBan::factory()
                ->bannedPlayer($players->random())
                ->bannedBy($staffPlayers->random())
                ->create();
        }
    }
}
