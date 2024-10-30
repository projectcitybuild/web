<?php

namespace Database\Seeders;

use App\Models\MinecraftConfig;
use App\Models\MinecraftWarp;
use App\Models\Server;
use App\Models\ServerToken;
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
        $this->call(ShowcaseWarpSeeder::class);

        ServerToken::create([
            'token' => 'pcbridge_local',
            'server_id' => Server::first()->getKey(),
            'description' => 'For test use',
        ]);

        MinecraftConfig::factory()
            ->create();

        MinecraftWarp::factory()
            ->count(40)
            ->create();
    }
}
