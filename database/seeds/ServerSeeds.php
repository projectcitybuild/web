<?php

use Illuminate\Database\Seeder;

use App\Modules\Servers\Models\{ServerCategory, Server};

class ServerSeeds extends Seeder {
    
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $categoryMinecraft = ServerCategory::create([
            'name'          => 'minecraft',
            'display_order' => 1,
        ]);
        
        $categoryOtherGames = ServerCategory::create([
            'name'          => 'other games',
            'display_order' => 2,
        ]);

        Server::create([
            'name'                  => 'Survival / Creative [24/7]',
            'server_category_id'    => $categoryMinecraft->server_category_id,
            'game_type'             => 'minecraft',
            'ip'                    => '198.144.156.53',
            'ip_alias'              => 'pcbmc.co',
            'port'                  => '25565',
            'is_port_visible'       => true,
            'is_querying'           => true,
            'is_visible'            => true,
            'display_order'         => 1,
        ]);

        Server::create([
            'name'                  => 'Feed the Beast',
            'server_category_id'    => $categoryMinecraft->server_category_id,
            'game_type'             => 'minecraft',
            'ip'                    => '23.94.186.178',
            'port'                  => '25565',
            'is_port_visible'       => true,
            'is_querying'           => true,
            'is_visible'            => true,
            'display_order'         => 2,
        ]);
    }
}
