<?php

namespace Database\Seeders;

use Entities\Models\Eloquent\Account;
use Entities\Models\Eloquent\GameIPBan;
use Entities\Models\Eloquent\GamePlayerBan;
use Entities\Models\Eloquent\MinecraftPlayer;
use Illuminate\Database\Seeder;

class GameBanSeeder extends Seeder
{
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

        $staffPlayersWithoutNull = $staffPlayers->filter(fn ($it) => $it !== null);
        for ($i = 0; $i < 25; $i++) {
            GameIPBan::factory()
                ->bannedBy($staffPlayersWithoutNull->random())
                ->create();
        }
    }
}
