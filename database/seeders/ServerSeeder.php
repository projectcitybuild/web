<?php

namespace Database\Seeders;

use App\Core\Data\GameType;
use App\Models\Server;
use Illuminate\Database\Seeder;

class ServerSeeder extends Seeder
{
    public function run()
    {
        $minecraftServer = Server::factory()->create([
            'name' => 'Minecraft (Java)',
            'game_type' => GameType::MINECRAFT->value,
            'ip' => '158.69.120.168',
            'ip_alias' => 'pcbmc.co',
            'port' => '25565',
            'display_order' => 1,
            'is_online' => true,
            'num_of_players' => 45,
            'num_of_slots' => 100,
        ]);

        Server::factory()->create([
            'name' => 'Feed the Beast',
            'game_type' => GameType::MINECRAFT->value,
            'is_querying' => false,
            'display_order' => 2,
        ]);
    }
}
