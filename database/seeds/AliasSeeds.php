<?php

use Illuminate\Database\Seeder;

use App\Modules\Users\Models\UserAliasType;

class AliasSeeds extends Seeder {
    
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        UserAliasType::create([
            'name' => 'MINECRAFT_UUID',
        ]);

        UserAliasType::create([
            'name' => 'MINECRAFT_NAME',
        ]);

        UserAliasType::create([
            'name' => 'STEAM_ID',
        ]);
    }
}
