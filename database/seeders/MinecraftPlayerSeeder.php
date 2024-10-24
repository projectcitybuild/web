<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\MinecraftPlayer;
use App\Models\MinecraftPlayerAlias;
use Illuminate\Database\Seeder;

class MinecraftPlayerSeeder extends Seeder
{
    public function run()
    {
        $accounts = Account::get();

        $j = 0;
        for ($i = 0; $i < 100; $i++) {
            $player = MinecraftPlayer::factory()
                ->for(Account::factory(), 'account')
                ->create([
                    'account_id' => (rand(0, 1) === 1) ? $accounts[$j++]->getKey() : null,
                ]);

            if (rand(0, 1) === 1) {
                MinecraftPlayerAlias::factory()
                    ->for($player, 'minecraftPlayer')
                    ->create();
            }
        }
    }
}
