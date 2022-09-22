<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call(ServerSeeder::class);
        $this->call(GroupSeeder::class);
        $this->call(AccountSeeder::class);
        $this->call(MinecraftPlayerSeeder::class);
        $this->call(GameBanSeeder::class);
        $this->call(PlayerWarningSeeder::class);
        $this->call(DonationSeeder::class);
        $this->call(BadgeSeeder::class);
        $this->call(ServerTokenSeeder::class);
    }
}
