<?php

use Illuminate\Database\Seeder;

use App\Entities\Eloquent\Groups\Models\Group;
use App\Entities\Eloquent\Groups\GroupEnum;

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
            'name' => GroupEnum::Retired,
        ]);

        Group::create([
            'name' => GroupEnum::Member,
            'is_default' => true,
        ]);

        Group::create([
            'name' => GroupEnum::Trusted,
        ]);
        
        Group::create([
            'name' => GroupEnum::Donator,
        ]);

        Group::create([
            'name' => GroupEnum::Moderator,
            'alias' => 'Mod',
            'is_staff' => true,
        ]);
        
        Group::create([
            'name' => GroupEnum::Operator,
            'alias' => 'OP',
            'is_staff' => true,
        ]);
        
        Group::create([
            'name' => GroupEnum::SeniorOperator,
            'alias' => 'SOP',
            'is_staff' => true,
        ]);

        Group::create([
            'name' => GroupEnum::Administrator,
            'alias' => 'Admin',
            'is_staff' => true,
            'is_admin' => true,
        ]);
    }
}
