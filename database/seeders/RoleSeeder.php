<?php

namespace Database\Seeders;

use App\Domains\Permissions\WebManagePermission;
use App\Domains\Permissions\WebReviewPermission;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run()
    {
        Role::factory()->create([
            'name' => 'member',
            'minecraft_name' => 'default',
            'minecraft_display_name' => '<gray>[M]</gray>',
            'minecraft_hover_text' => 'Member',
            'is_default' => true,
            'role_type' => 'trust',
            'display_priority' => 1,
            'additional_homes' => 3,
        ]);

        Role::factory()->create([
            'name' => 'trusted',
            'minecraft_name' => 'trusted',
            'minecraft_display_name' => '<blue>[T]</blue>',
            'minecraft_hover_text' => 'Trusted',
            'role_type' => 'trust',
            'display_priority' => 2,
        ]);

        Role::factory()->create([
            'name' => 'trusted+',
            'minecraft_name' => 'trusted_plus',
            'minecraft_display_name' => '<purple>[T+]</purple>',
            'minecraft_hover_text' => 'Trusted+',
            'role_type' => 'trust',
            'display_priority' => 3,
        ]);

        Role::factory()->create([
            'name' => 'intern',
            'minecraft_name' => 'intern',
            'minecraft_display_name' => '<dark_gray>[I]</dark_gray>',
            'minecraft_hover_text' => 'Intern',
            'role_type' => 'build',
            'display_priority' => 1,
            'additional_homes' => 1,
        ]);

        Role::factory()->create([
            'name' => 'builder',
            'minecraft_name' => 'builder',
            'minecraft_display_name' => '<dark_gray>[B]</dark_gray>',
            'minecraft_hover_text' => 'Builder',
            'role_type' => 'build',
            'display_priority' => 2,
            'additional_homes' => 3,
        ]);

        Role::factory()->create([
            'name' => 'planner',
            'minecraft_name' => 'planner',
            'minecraft_display_name' => '<dark_gray>[P]</dark_gray>',
            'minecraft_hover_text' => 'Planner',
            'role_type' => 'build',
            'display_priority' => 3,
            'additional_homes' => 5,
        ]);

        Role::factory()->create([
            'name' => 'engineer',
            'minecraft_name' => 'engineer',
            'minecraft_display_name' => '<dark_gray>[E]</dark_gray>',
            'minecraft_hover_text' => 'Engineer',
            'role_type' => 'build',
            'display_priority' => 4,
            'additional_homes' => 7,
        ]);

        $architect = Role::factory()->create([
            'name' => 'architect',
            'minecraft_name' => 'architect',
            'minecraft_display_name' => '<dark_gray>[A]</dark_gray>',
            'minecraft_hover_text' => 'Architect',
            'role_type' => 'build',
            'display_priority' => 5,
            'additional_homes' => 10,
        ]);

        Role::factory()->create([
            'name' => 'donator',
            'minecraft_name' => 'donator',
            'minecraft_display_name' => '<green>[$]</green>',
            'minecraft_hover_text' => 'Donator',
            'role_type' => 'donor',
            'display_priority' => 1,
        ]);

        Role::factory()->create([
            'name' => 'legacy donor',
            'minecraft_name' => 'legacy-donor',
            'minecraft_display_name' => '<green>[$]</green>',
            'minecraft_hover_text' => 'Donator (Legacy)',
            'role_type' => 'donor',
            'display_priority' => 2,
        ]);

        $mod = Role::factory()->create([
            'name' => 'moderator',
            'minecraft_name' => 'moderator',
            'minecraft_display_name' => '<red>[Staff]</red>',
            'minecraft_hover_text' => 'Moderator',
            'alias' => 'Mod',
            'role_type' => 'staff',
            'display_priority' => 1,
            'additional_homes' => 5,
        ]);

        $dev = Role::factory()->create([
            'name' => 'developer',
            'minecraft_name' => 'develop',
            'minecraft_display_name' => '<red>[Staff]</red>',
            'minecraft_hover_text' => 'Developer',
            'alias' => 'Dev',
            'is_admin' => true,
            'role_type' => 'staff',
            'display_priority' => 2,
        ]);

        $permissions = [];
        foreach (WebManagePermission::cases() as $node) {
            $permissions[$node->value] = Permission::create(['name' => $node]);
        }
        foreach (WebReviewPermission::cases() as $node) {
            $permissions[$node->value] = Permission::create(['name' => $node]);
        }

        $dev->permissions()->attach(
            array_values($permissions),
        );

        $mod->permissions()->attach(
            collect([
                WebManagePermission::ROLES_VIEW,
                WebManagePermission::ACCOUNTS_VIEW,
                WebManagePermission::UUID_BANS_VIEW,
                WebManagePermission::UUID_BANS_EDIT,
                WebManagePermission::IP_BANS_VIEW,
                WebManagePermission::IP_BANS_EDIT,
                WebManagePermission::WARNINGS_VIEW,
                WebManagePermission::WARNINGS_EDIT,
                WebManagePermission::PLAYERS_VIEW,
                WebManagePermission::PLAYERS_EDIT,
                WebManagePermission::BADGES_VIEW,
                WebManagePermission::BADGES_EDIT,
                WebManagePermission::WARPS_VIEW,
                WebManagePermission::WARPS_EDIT,
                WebManagePermission::HOMES_VIEW,
                WebManagePermission::HOMES_EDIT,
                WebReviewPermission::BAN_APPEALS_VIEW,
                WebReviewPermission::BAN_APPEALS_DECIDE,
                WebReviewPermission::BUILD_RANK_APPS_VIEW,
                WebReviewPermission::BUILD_RANK_APPS_DECIDE,
            ])
            ->map(fn ($it) => $permissions[$it->value]),
        );

        $architect->permissions()->attach(
            collect([
                WebReviewPermission::BUILD_RANK_APPS_VIEW,
                WebReviewPermission::BUILD_RANK_APPS_DECIDE,
            ])
            ->map(fn ($it) => $permissions[$it->value]),
        );
    }
}
