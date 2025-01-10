<?php

namespace Database\Seeders;

use App\Domains\BuilderRankApplications\Data\ApplicationStatus;
use App\Models\Account;
use App\Models\BanAppeal;
use App\Models\BuilderRankApplication;
use App\Models\GamePlayerBan;
use App\Models\MinecraftBuild;
use App\Models\MinecraftConfig;
use App\Models\MinecraftPlayer;
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

        $this->call(GroupSeeder::class);
        $this->call(AccountSeeder::class);
        $this->call(MinecraftPlayerSeeder::class);
        $this->call(GameBanSeeder::class);
        $this->call(PlayerWarningSeeder::class);
        $this->call(DonationSeeder::class);
        $this->call(BadgeSeeder::class);
        $this->call(BanAppealSeeder::class);

        ServerToken::create([
            'token' => 'pcbridge_local',
            'server_id' => Server::first()->getKey(),
            'description' => 'For test use',
        ]);

        MinecraftConfig::factory()
            ->create();

        MinecraftWarp::factory()
            ->count(50)
            ->create();

        $players = MinecraftPlayer::get();
        for ($i = 0; $i < 50; $i++) {
            MinecraftBuild::factory()
                ->create(['player_id' => $players->random()]);
        }

        for ($i = 0; $i < 5; $i++) {
            BuilderRankApplication::factory()
                ->for(Account::inRandomOrder()->first())
                ->create();
        }

        BuilderRankApplication::factory()
            ->for(Account::inRandomOrder()->first())
            ->status(ApplicationStatus::APPROVED)
            ->create();

        BuilderRankApplication::factory()
            ->for(Account::inRandomOrder()->first())
            ->status(ApplicationStatus::DENIED)
            ->create();
    }
}
