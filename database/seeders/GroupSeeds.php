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
            'name' => 'intern',
            'is_build' => true,
        ]);

        Group::create([
            'name' => 'builder',
            'is_build' => true,
        ]);

        Group::create([
            'name' => 'planner',
            'is_build' => true,
        ]);

        Group::create([
            'name' => 'engineer',
            'is_build' => true,
        ]);

        Group::create([
            'name' => 'architect',
            'is_build' => true,
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
            'name' => 'developer',
            'alias' => 'Dev',
            'is_staff' => true,
            'is_admin' => true,
            'can_access_panel' => true,
        ]);
    }
}
