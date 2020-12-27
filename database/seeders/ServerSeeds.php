<?php

namespace Database\Seeders;

use App\Entities\GameType;
use App\Entities\ServerKeys\Models\ServerKey;
use App\Entities\Servers\Models\Server;
use App\Entities\Servers\Models\ServerCategory;
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

        $categoryOtherGames = ServerCategory::factory()->create([
            'name'          => 'other games',
            'display_order' => 2,
        ]);

        $minecraftServer = Server::factory()->create([
            'name'                  => 'Survival / Creative [24/7]',
            'server_category_id'    => $categoryMinecraft->server_category_id,
            'game_type'             => GameType::Minecraft,
            'ip'                    => '198.144.156.53',
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

        Server::factory()->create([
            'name'                  => 'Pixelmon',
            'server_category_id'    => $categoryMinecraft->server_category_id,
            'game_type'             => GameType::Minecraft,
            'is_querying'           => false,
            'display_order'         => 3,
        ]);

        Server::factory()->create([
            'name'                  => 'Terraria',
            'server_category_id'    => $categoryOtherGames->server_category_id,
            'game_type'             => GameType::Terraria,
            'is_querying'           => false,
            'display_order'         => 1,
        ]);

        Server::factory()->create([
            'name'                  => 'Starbound',
            'server_category_id'    => $categoryOtherGames->server_category_id,
            'game_type'             => GameType::Starbound,
            'is_querying'           => false,
            'display_order'         => 2,
        ]);

        $serverKey = ServerKey::create([
            'server_id' => $minecraftServer->server_id,
            'token' => bin2hex(random_bytes(30)),
            'can_local_ban' => true,
            'can_global_ban' => true,
            'can_warn' => true,
        ]);
    }
}
