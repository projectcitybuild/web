<?php

namespace Database\Seeders;

use Entities\Models\Eloquent\Account;
use Entities\Models\Eloquent\MinecraftPlayer;
use Entities\Models\Eloquent\MinecraftPlayerAlias;
use Entities\Models\Eloquent\PlayerWarning;
use Illuminate\Database\Seeder;

class PlayerWarningSeeder extends Seeder
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
            tap(MinecraftPlayer::factory()->create(), fn ($player) => MinecraftPlayerAlias::factory()->for($player)->create()),
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
