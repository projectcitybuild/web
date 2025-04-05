<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\BuilderRankApplication;
use Illuminate\Database\Seeder;

class BuilderRankApplicationSeeder extends Seeder
{
    public function run()
    {
        $this->createPending(15);
        $this->createApproved(5);
        $this->createDenied(20);
    }

    private function createPending(int $count)
    {
        for ($i = 0; $i < $count; $i++) {
            BuilderRankApplication::factory()
                ->for(Account::inRandomOrder()->first())
                ->create();
        }
    }

    private function createApproved(int $count)
    {
        for ($i = 0; $i < $count; $i++) {
            BuilderRankApplication::factory()
                ->for(Account::inRandomOrder()->first())
                ->approved()
                ->create();
        }
    }

    private function createDenied(int $count)
    {
        for ($i = 0; $i < $count; $i++) {
            BuilderRankApplication::factory()
                ->for(Account::inRandomOrder()->first())
                ->denied()
                ->create();
        }
    }
}
