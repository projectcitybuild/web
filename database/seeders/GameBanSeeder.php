<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\GameIPBan;
use App\Models\GamePlayerBan;
use App\Models\MinecraftPlayer;
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

        for ($i = 0; $i < 50; $i++) {
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
