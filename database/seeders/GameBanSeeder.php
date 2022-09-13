<?php

namespace Database\Seeders;

use Entities\Models\Eloquent\Account;
use Entities\Models\Eloquent\GameBan;
use Entities\Models\Eloquent\MinecraftPlayer;
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
        $staffPlayers = collect([
            MinecraftPlayer::factory()->create(),
            MinecraftPlayer::factory()->for(Account::factory())->create(),
            null,
        ]);

        $players = MinecraftPlayer::get();

        for ($i = 0; $i < 100; $i++) {
            GameBan::factory()
                ->bannedPlayer($players->random())
                ->bannedBy($staffPlayers->random())
                ->create();
        }
    }
}
