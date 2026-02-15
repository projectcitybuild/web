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

        $viewRoles = Permission::firstOrCreate([
            'name' => 'web.manage.roles.view',
            'description' => 'Can view roles',
        ]);

        $editRoles = Permission::firstOrCreate([
            'name' => 'web.manage.roles.edit',
            'description' => 'Can edit roles',
        ]);

        $assignRoles = Permission::firstOrCreate([
            'name' => 'web.manage.roles.assign',
            'description' => 'Can assign roles',
        ]);

        $viewAccounts = Permission::firstOrCreate([
            'name' => 'web.manage.accounts.view',
            'description' => 'Can view accounts',
        ]);

        $editAccounts = Permission::firstOrCreate([
            'name' => 'web.manage.accounts.edit',
            'description' => 'Can edit accounts',
        ]);

        $viewUUIDBans = Permission::firstOrCreate([
            'name' => 'web.manage.uuid_bans.view',
            'description' => 'Can view UUID bans',
        ]);

        $editUUIDBans = Permission::firstOrCreate([
            'name' => 'web.manage.uuid_bans.edit',
            'description' => 'Can edit UUID bans',
        ]);

        $viewIPBans = Permission::firstOrCreate([
            'name' => 'web.manage.ip_bans.view',
            'description' => 'Can view IP bans',
        ]);

        $editIPBans = Permission::firstOrCreate([
            'name' => 'web.manage.ip_bans.edit',
            'description' => 'Can edit IP bans',
        ]);

        $viewWarnings = Permission::firstOrCreate([
            'name' => 'web.manage.warnings.view',
            'description' => 'Can view warnings',
        ]);

        $editWarnings = Permission::firstOrCreate([
            'name' => 'web.manage.warnings.edit',
            'description' => 'Can edit warnings',
        ]);

        $viewPlayers = Permission::firstOrCreate([
            'name' => 'web.manage.players.view',
            'description' => 'Can view players',
        ]);

        $editPlayers = Permission::firstOrCreate([
            'name' => 'web.manage.players.edit',
            'description' => 'Can edit players',
        ]);

        $viewBadges = Permission::firstOrCreate([
            'name' => 'web.manage.badges.view',
            'description' => 'Can view badges',
        ]);

        $editBadges = Permission::firstOrCreate([
            'name' => 'web.manage.badges.edit',
            'description' => 'Can edit badges',
        ]);

        $viewWarps = Permission::firstOrCreate([
            'name' => 'web.manage.warps.view',
            'description' => 'Can view warps',
        ]);

        $editWarps = Permission::firstOrCreate([
            'name' => 'web.manage.warps.edit',
            'description' => 'Can edit warps',
        ]);

        $viewServers = Permission::firstOrCreate([
            'name' => 'web.manage.servers.view',
            'description' => 'Can view servers',
        ]);

        $editServers = Permission::firstOrCreate([
            'name' => 'web.manage.servers.edit',
            'description' => 'Can edit servers',
        ]);

        $viewServerTokens = Permission::firstOrCreate([
            'name' => 'web.manage.server_tokens.view',
            'description' => 'Can view server tokens',
        ]);

        $editServerTokens = Permission::firstOrCreate([
            'name' => 'web.manage.server_tokens.edit',
            'description' => 'Can edit server tokens',
        ]);

        $editRemoteConfig = Permission::firstOrCreate([
            'name' => 'web.manage.remote_config.edit',
            'description' => 'Can edit remote config',
        ]);

        $viewDonations = Permission::firstOrCreate([
            'name' => 'web.manage.donations.view',
            'description' => 'Can view donations',
        ]);

        $editDonations = Permission::firstOrCreate([
            'name' => 'web.manage.donations.edit',
            'description' => 'Can edit donations',
        ]);

        $dev->permissions()->attach([
            $viewRoles,
            $editRoles,
            $assignRoles,
            $viewAccounts,
            $editAccounts,
            $viewUUIDBans,
            $editUUIDBans,
            $viewIPBans,
            $editIPBans,
            $viewWarnings,
            $editWarnings,
            $viewPlayers,
            $editPlayers,
            $viewBadges,
            $editBadges,
            $viewWarps,
            $editWarps,
            $viewServers,
            $editServers,
            $viewServerTokens,
            $editServerTokens,
            $editRemoteConfig,
            $viewDonations,
            $editDonations,
        ]);

        $mod->permissions()->attach([
            $viewRoles,
            $viewAccounts,
            $viewUUIDBans,
            $editUUIDBans,
            $viewIPBans,
            $editIPBans,
            $viewWarnings,
            $editWarnings,
            $viewPlayers,
            $editPlayers,
            $viewBadges,
            $editBadges,
            $viewWarps,
            $editWarps,
        ]);

        $architect->permissions()->attach([

        ]);
    }
}
