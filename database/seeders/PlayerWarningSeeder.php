<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\MinecraftPlayer;
use App\Models\PlayerWarning;
use Illuminate\Database\Seeder;

class PlayerWarningSeeder extends Seeder
{
    public function run()
    {
        $staffPlayers = collect([
            MinecraftPlayer::factory()->create(),
            MinecraftPlayer::factory()->for(Account::factory())->create(),
        ]);

        $players = MinecraftPlayer::get();

        for ($i = 0; $i < 15; $i++) {
            PlayerWarning::factory()
                ->warnedPlayer($players->random())
                ->warnedBy($staffPlayers->random())
                ->create();
        }
    }
}
