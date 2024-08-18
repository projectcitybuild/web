<?php

namespace Database\Seeders;

use Entities\Models\Eloquent\Server;
use Entities\Models\Eloquent\ServerCategory;
use Entities\Models\GameType;
use Illuminate\Database\Seeder;

class ServerSeeder extends Seeder
{
    public function run()
    {
        $categoryMinecraft = ServerCategory::factory()->create([
            'name' => 'minecraft',
            'display_order' => 1,
        ]);

        $minecraftServer = Server::factory()->create([
            'name' => 'Minecraft (Java)',
            'server_category_id' => $categoryMinecraft->server_category_id,
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
            'server_category_id' => $categoryMinecraft->server_category_id,
            'game_type' => GameType::MINECRAFT->value,
            'is_querying' => false,
            'display_order' => 2,
        ]);
    }
}