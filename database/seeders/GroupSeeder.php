<?php

namespace Database\Seeders;

use App\Domains\Panel\Data\PanelGroupScope;
use App\Models\Group;
use App\Models\GroupScope;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    public function run()
    {
        $scopes = collect();

        collect(PanelGroupScope::cases())->each(function ($scope) use (&$scopes) {
            $model = GroupScope::create([
                'scope' => $scope->value,
            ]);
            $scopes->put(key: $scope->value, value: $model->getKey());
        });

        Group::factory()->create([
            'name' => 'member',
            'minecraft_name' => 'default',
            'is_default' => true,
            'group_type' => 'trust',
            'display_priority' => 1,
        ]);

        Group::factory()->create([
            'name' => 'trusted',
            'minecraft_name' => 'trusted',
            'group_type' => 'trust',
            'display_priority' => 2,
        ]);

        Group::factory()->create([
            'name' => 'trusted+',
            'minecraft_name' => 'trusted_plus',
            'group_type' => 'trust',
            'display_priority' => 3,
        ]);

        Group::factory()->create([
            'name' => 'intern',
            'minecraft_name' => 'intern',
            'is_build' => true,
            'group_type' => 'build',
            'display_priority' => 1,
        ]);

        Group::factory()->create([
            'name' => 'builder',
            'minecraft_name' => 'builder',
            'is_build' => true,
            'group_type' => 'build',
            'display_priority' => 2,
        ]);

        Group::factory()->create([
            'name' => 'planner',
            'minecraft_name' => 'planner',
            'is_build' => true,
            'group_type' => 'build',
            'display_priority' => 3,
        ]);

        Group::factory()->create([
            'name' => 'engineer',
            'minecraft_name' => 'engineer',
            'is_build' => true,
            'group_type' => 'build',
            'display_priority' => 4,
        ]);

        $architect = Group::factory()->create([
            'name' => 'architect',
            'minecraft_name' => 'architect',
            'is_build' => true,
            'group_type' => 'build',
            'display_priority' => 5,
        ]);
        $architect->groupScopes()->attach([
            $scopes[PanelGroupScope::ACCESS_PANEL->value],
            $scopes[PanelGroupScope::REVIEW_BUILD_RANK_APPS->value],
        ]);

        Group::factory()->create([
            'name' => 'donator',
            'minecraft_name' => 'donator',
            'group_type' => 'donor',
            'display_priority' => 1,
        ]);

        Group::factory()->create([
            'name' => 'legacy donor',
            'minecraft_name' => 'legacy-donor',
            'group_type' => 'donor',
            'display_priority' => 2,
        ]);

        $mod = Group::factory()->create([
            'name' => 'moderator',
            'minecraft_name' => 'moderator',
            'alias' => 'Mod',
            'is_staff' => true,
            'group_type' => 'staff',
            'display_priority' => 1,
        ]);
        $mod->groupScopes()->attach([
            $scopes[PanelGroupScope::ACCESS_PANEL->value],
            $scopes[PanelGroupScope::REVIEW_APPEALS->value],
            $scopes[PanelGroupScope::REVIEW_BUILD_RANK_APPS->value],
        ]);

        $dev = Group::factory()->create([
            'name' => 'developer',
            'minecraft_name' => 'develop',
            'alias' => 'Dev',
            'is_staff' => true,
            'is_admin' => true,
            'can_access_panel' => true,
            'group_type' => 'staff',
            'display_priority' => 2,
        ]);
        $dev->groupScopes()->attach(
            collect($scopes)->values()->toArray(),
        );
    }
}
