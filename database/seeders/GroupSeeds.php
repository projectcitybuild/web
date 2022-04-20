<?php

namespace Database\Seeders;

use Entities\Models\Eloquent\Group;
use Illuminate\Database\Seeder;

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
            'name' => 'retired',
            'discourse_name' => 'retired',
        ]);

        Group::create([
            'name' => 'member',
            'is_default' => true,
            'discourse_name' => null,
        ]);

        Group::create([
            'name' => 'trusted',
            'discourse_name' => 'trusted',
        ]);

        Group::create([
            'name' => 'trusted+',
            'discourse_name' => 'trusted-plus',
        ]);

        Group::create([
            'name' => 'donator',
            'discourse_name' => 'donator',
        ]);

        Group::create([
            'name' => 'legacy donor',
            'minecraft_name' => 'legacy-donor',
            'discourse_name' => 'legacy-donor',
        ]);

        Group::create([
            'name' => 'moderator',
            'alias' => 'Mod',
            'is_staff' => true,
            'discourse_name' => 'moderator',
        ]);

        Group::create([
            'name' => 'operator',
            'alias' => 'OP',
            'is_staff' => true,
            'discourse_name' => 'operator',
        ]);

        Group::create([
            'name' => 'senior operator',
            'alias' => 'SOP',
            'is_staff' => true,
            'discourse_name' => 'senior-operator',
        ]);

        Group::create([
            'name' => 'administrator',
            'alias' => 'Admin',
            'is_staff' => true,
            'is_admin' => true,
            'discourse_name' => 'administrator',
            'can_access_panel' => true,
        ]);
    }
}
