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

        Group::create([
            'name' => 'retired',
        ]);

        Group::create([
            'name' => 'member',
            'is_default' => true,
        ]);

        Group::create([
            'name' => 'trusted',
        ]);

        Group::create([
            'name' => 'trusted+',
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

        $architect = Group::create([
            'name' => 'architect',
            'is_build' => true,
        ]);
        $architect->groupScopes()->attach([
            $scopes[PanelGroupScope::ACCESS_PANEL->value],
            $scopes[PanelGroupScope::REVIEW_BUILD_RANK_APPS->value],
        ]);

        Group::create([
            'name' => 'donator',
        ]);

        Group::create([
            'name' => 'legacy donor',
            'minecraft_name' => 'legacy-donor',
        ]);

        $mod = Group::create([
            'name' => 'moderator',
            'alias' => 'Mod',
            'is_staff' => true,
        ]);
        $mod->groupScopes()->attach([
            $scopes[PanelGroupScope::ACCESS_PANEL->value],
            $scopes[PanelGroupScope::REVIEW_APPEALS->value],
            $scopes[PanelGroupScope::REVIEW_BUILD_RANK_APPS->value],
        ]);

        $dev = Group::create([
            'name' => 'developer',
            'alias' => 'Dev',
            'is_staff' => true,
            'is_admin' => true,
            'can_access_panel' => true,
        ]);
        $dev->groupScopes()->attach(
            collect($scopes)->values()->toArray(),
        );
    }
}
