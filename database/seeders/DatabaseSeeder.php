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
        Server::factory()->create([
            'name' => 'Minecraft (Java)',
            'ip' => 'host.docker.internal',
            'ip_alias' => 'pcbmc.co',
            'port' => '25565',
            'web_port' => '8080',
        ]);

        ServerToken::create([
            'token' => 'pcbridge_local',
            'server_id' => Server::first()->getKey(),
            'description' => 'For test use',
        ]);

        $this->call(GroupSeeder::class);
        $this->call(AccountSeeder::class);
        $this->call(MinecraftPlayerSeeder::class);
        $this->call(GameBanSeeder::class);
        $this->call(PlayerWarningSeeder::class);
        $this->call(DonationSeeder::class);
        $this->call(BadgeSeeder::class);
        $this->call(BanAppealSeeder::class);
        $this->call(BuilderRankApplicationSeeder::class);

        MinecraftConfig::factory()
            ->create();

        MinecraftWarp::factory()
            ->count(50)
            ->create();
    }
}
