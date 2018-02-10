<?php

use Illuminate\Database\Seeder;

use App\Modules\Servers\Models\{ServerCategory, Server};
use App\Modules\ServerKeys\Models\{ServerKey, ServerKeyToken};
use App\Modules\Servers\GameTypeEnum;
use App\Modules\Servers\Repositories\ServerKeyTokenRepository;

class ServerSeeds extends Seeder {
    
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $categoryMinecraft = factory(ServerCategory::class)->create([
            'name'          => 'minecraft',
            'display_order' => 1,
        ]);
        
        $categoryOtherGames = factory(ServerCategory::class)->create([
            'name'          => 'other games',
            'display_order' => 2,
        ]);

        $minecraftServer = factory(Server::class)->create([
            'name'                  => 'Survival / Creative [24/7]',
            'server_category_id'    => $categoryMinecraft->server_category_id,
            'game_type'             => GameTypeEnum::Minecraft,
            'ip'                    => '198.144.156.53',
            'ip_alias'              => 'pcbmc.co',
            'port'                  => '25565',
            'display_order'         => 1,
        ]);

        factory(Server::class)->create([
            'name'                  => 'Feed the Beast',
            'server_category_id'    => $categoryMinecraft->server_category_id,
            'game_type'             => GameTypeEnum::Minecraft,
            'is_querying'           => false,
            'display_order'         => 2,
        ]);

        factory(Server::class)->create([
            'name'                  => 'Pixelmon',
            'server_category_id'    => $categoryMinecraft->server_category_id,
            'game_type'             => GameTypeEnum::Minecraft,
            'is_querying'           => false,
            'display_order'         => 3,
        ]);

        factory(Server::class)->create([
            'name'                  => 'Terraria',
            'server_category_id'    => $categoryOtherGames->server_category_id,
            'game_type'             => GameTypeEnum::Terraria,
            'is_querying'           => false,
            'display_order'         => 1,
        ]);
        
        factory(Server::class)->create([
            'name'                  => 'Starbound',
            'server_category_id'    => $categoryOtherGames->server_category_id,
            'game_type'             => GameTypeEnum::Starbound,
            'is_querying'           => false,
            'display_order'         => 2,
        ]);

        
        $serverKey = ServerKey::create([
            'server_id' => $minecraftServer->server_id,
            'can_local_ban' => true,
            'can_global_ban' => true,
            'can_access_ranks' => true,
        ]);

        $token = ServerKeyToken::create([
            'server_key_id' => $serverKey->server_key_id,
            'token_hash' => bin2hex(random_bytes(30)),
            'is_blacklisted' => false,
        ]);
    }
}
