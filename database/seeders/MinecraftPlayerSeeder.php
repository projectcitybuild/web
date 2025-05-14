<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\MinecraftBuild;
use App\Models\MinecraftHome;
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

        $players = MinecraftPlayer::get();
        $this->createBuilds($players);
        $this->createHomes($players);
    }

    private function createBuilds($players)
    {
        for ($i = 0; $i < 50; $i++) {
            MinecraftBuild::factory()
                ->create(['player_id' => $players->random()]);
        }
    }

    private function createHomes($players)
    {
        for ($i = 0; $i < 50; $i++) {
            $player = $players->random();

            MinecraftHome::factory(rand(1, 6))
                ->create(['player_id' => $player]);
        }
    }
}
