<?php

namespace Database\Seeders;

use App\Models\MinecraftConfig;
use App\Models\MinecraftWarp;
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
        $this->call(ShowcaseWarpSeeder::class);

        MinecraftConfig::factory()
            ->create();

        MinecraftWarp::factory()
            ->count(40)
            ->create();
    }
}
