<?php

namespace Database\Seeders;

use App\Models\BanAppeal;
use App\Models\GamePlayerBan;
use Illuminate\Database\Seeder;

class BanAppealSeeder extends Seeder
{
    public function run()
    {
        $this->createPending(15);
        $this->createUnbanned(20);
        $this->createDenied(20);
    }

    private function createPending(int $count)
    {
        for ($i = 0; $i < $count; $i++) {
            BanAppeal::factory()
                ->for(GamePlayerBan::inRandomOrder()->first())
                ->create();
        }
    }

    private function createUnbanned(int $count)
    {
        for ($i = 0; $i < $count; $i++) {
            BanAppeal::factory()
                ->for(GamePlayerBan::inRandomOrder()->first())
                ->unbanned()
                ->create();
        }
    }

    private function createDenied(int $count)
    {
        for ($i = 0; $i < $count; $i++) {
            BanAppeal::factory()
                ->for(GamePlayerBan::inRandomOrder()->first())
                ->denied()
                ->create();
        }
    }
}
