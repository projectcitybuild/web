<?php

namespace Database\Seeders;

use App\Entities\Models\Eloquent\Server;
use App\Entities\Models\Eloquent\ServerCategory;
use App\Entities\Models\Eloquent\ServerKey;
use App\Entities\Models\Eloquent\ServerStatus;
use App\Entities\Models\GameType;
use Illuminate\Database\Seeder;

class ServerSeeds extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categoryMinecraft = ServerCategory::factory()->create([
            'name'          => 'minecraft',
            'display_order' => 1,
        ]);

        $minecraftServer = Server::factory()->create([
            'name'                  => 'Minecraft (Java)',
            'server_category_id'    => $categoryMinecraft->server_category_id,
            'game_type'             => GameType::Minecraft,
            'ip'                    => '158.69.120.168',
            'ip_alias'              => 'pcbmc.co',
            'port'                  => '25565',
            'display_order'         => 1,
        ]);

        Server::factory()->create([
            'name'                  => 'Feed the Beast',
            'server_category_id'    => $categoryMinecraft->server_category_id,
            'game_type'             => GameType::Minecraft,
            'is_querying'           => false,
            'display_order'         => 2,
        ]);

        ServerKey::create([
            'server_id' => $minecraftServer->server_id,
            'token' => bin2hex(random_bytes(30)),
            'can_local_ban' => true,
            'can_global_ban' => true,
            'can_warn' => true,
        ]);

        ServerStatus::create([
            'server_id' => $minecraftServer->getKey(),
            'is_online' => true,
            'num_of_players' => 45,
            'num_of_slots' => 100,
        ]);
    }
}
