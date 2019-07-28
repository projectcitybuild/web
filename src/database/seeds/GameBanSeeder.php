<?php

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
        factory(App\Entities\Accounts\Models\Account::class, 50)->create()->each(function ($account) {
            $mcAccount = $account->minecraftAccount()
                    ->save(factory(\App\Entities\Players\Models\MinecraftPlayer::class)
                    ->make());

            $mcAccount->aliases()->save(factory(\App\Entities\Players\Models\MinecraftPlayerAlias::class)->make());
        });
        factory(\App\Entities\Bans\Models\GameBan::class, 500)->create();
    }
}
