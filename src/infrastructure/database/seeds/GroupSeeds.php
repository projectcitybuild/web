<?php

use Illuminate\Database\Seeder;

use Domains\Modules\Servers\Models\ServerCategory;
use Domains\Modules\Servers\Models\Server;
use Domains\Modules\ServerKeys\Models\ServerKey;
use Domains\GameTypeEnum;
use Domains\Modules\Servers\Repositories\ServerKeyTokenRepository;
use Domains\Modules\Groups\Models\Group;

class GroupSeeds extends Seeder
{
    
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Group::create([
            'name' => 'Member',
            'is_default' => true,
        ]);

        Group::create([
            'name' => 'Trusted',
        ]);
        
        Group::create([
            'name' => 'Donator',
        ]);

        Group::create([
            'name' => 'Moderator',
            'alias' => 'Mod',
            'is_staff' => true,
        ]);
        
        Group::create([
            'name' => 'Operator',
            'alias' => 'OP',
            'is_staff' => true,
        ]);
        
        Group::create([
            'name' => 'Senior Operator',
            'alias' => 'SOP',
            'is_staff' => true,
        ]);

        Group::create([
            'name' => 'Administrator',
            'alias' => 'Admin',
            'is_staff' => true,
            'is_admin' => true,
        ]);
    }
}
