<?php

namespace Database\Seeders;

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

        $permManageRoles = Permission::create([
            'name' => 'web.manage.roles',
            'description' => 'Can manage roles',
        ]);
        $permManagePermissions = Permission::create([
            'name' => 'web.manage.permissions',
            'description' => 'Can manage permissions and their assignment to roles',
        ]);
        $permManageServerTokens = Permission::create([
            'name' => 'web.manage.server_tokens',
            'description' => 'Can manage server access tokens',
        ]);
        $permManageServers = Permission::create([
            'name' => 'web.manage.servers',
            'description' => 'Can manage servers',
        ]);
        $permManagePlayers = Permission::create([
            'name' => 'web.manage.players',
            'description' => 'Can manage players',
        ]);
        $permManageBadges = Permission::create([
            'name' => 'web.manage.badges',
            'description' => 'Can manage account badges',
        ]);
        $permManageAccounts = Permission::create([
            'name' => 'web.manage.accounts',
            'description' => 'Can manage accounts',
        ]);
        $permManageUuidBans = Permission::create([
            'name' => 'web.manage.bans.uuid',
            'description' => 'Can manage player UUID bans',
        ]);
        $permManageIpBans = Permission::create([
            'name' => 'web.manage.bans.ip',
            'description' => 'Can manage IP bans',
        ]);

        $permReviewBuildRankApps = Permission::create([
            'name' => 'web.review.build_rank_apps',
            'description' => 'Can review build rank applications',
        ]);
        $permReviewBanAppeals = Permission::create([
            'name' => 'web.review.ban_appeals',
            'description' => 'Can review ban appeals',
        ]);

        $dev->permissions()->attach([
            $permManageRoles,
            $permManagePermissions,
            $permManageServerTokens,
            $permManageServers,
            $permManagePlayers,
            $permManageBadges,
            $permManageAccounts,
            $permManageUuidBans,
            $permManageIpBans,
            $permReviewBanAppeals,
            $permReviewBuildRankApps,
        ]);
        $mod->permissions()->attach([
            $permManagePlayers,
            $permManageAccounts,
            $permManageBadges,
            $permManageUuidBans,
            $permManageIpBans,
            $permReviewBanAppeals,
            $permReviewBuildRankApps,
        ]);
        $architect->permissions()->attach([
            $permReviewBuildRankApps,
        ]);
    }
}
