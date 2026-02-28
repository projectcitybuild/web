<?php

namespace Database\Seeders;

use App\Domains\Permissions\WebManagePermission;
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

        $viewRoles = Permission::create([
            'name' => WebManagePermission::ROLES_VIEW,
        ]);

        $editRoles = Permission::create([
            'name' => WebManagePermission::ROLES_EDIT,
        ]);

        $assignRoles = Permission::create([
            'name' => WebManagePermission::ROLES_ASSIGN,
        ]);

        $viewPermissions = Permission::create([
            'name' => WebManagePermission::PERMISSIONS_VIEW,
        ]);

        $editPermissions = Permission::create([
            'name' => WebManagePermission::PERMISSIONS_EDIT,
        ]);

        $assignPermissions = Permission::create([
            'name' => WebManagePermission::PERMISSIONS_ASSIGN,
        ]);

        $viewAccounts = Permission::create([
            'name' => WebManagePermission::ACCOUNTS_VIEW,
        ]);

        $editAccounts = Permission::create([
            'name' => WebManagePermission::ACCOUNTS_EDIT,
        ]);

        $viewAccountEmails = Permission::create([
            'name' => WebManagePermission::ACCOUNTS_VIEW_EMAIL,
        ]);

        $viewUUIDBans = Permission::create([
            'name' => WebManagePermission::UUID_BANS_VIEW,
        ]);

        $editUUIDBans = Permission::create([
            'name' => WebManagePermission::UUID_BANS_EDIT,
        ]);

        $viewIPBans = Permission::create([
            'name' => WebManagePermission::IP_BANS_VIEW,
        ]);

        $editIPBans = Permission::create([
            'name' => WebManagePermission::IP_BANS_EDIT,
        ]);

        $viewWarnings = Permission::create([
            'name' => WebManagePermission::WARNINGS_VIEW,
        ]);

        $editWarnings = Permission::create([
            'name' => WebManagePermission::WARNINGS_EDIT,
        ]);

        $viewPlayers = Permission::create([
            'name' => WebManagePermission::PLAYERS_VIEW,
        ]);

        $editPlayers = Permission::create([
            'name' => WebManagePermission::PLAYERS_EDIT,
        ]);

        $viewPlayerIps = Permission::create([
            'name' => WebManagePermission::PLAYERS_VIEW_IPS,
        ]);

        $viewBadges = Permission::create([
            'name' => WebManagePermission::BADGES_VIEW,
        ]);

        $editBadges = Permission::create([
            'name' => WebManagePermission::BADGES_EDIT,
        ]);

        $viewWarps = Permission::create([
            'name' => WebManagePermission::WARPS_VIEW,
        ]);

        $editWarps = Permission::create([
            'name' => WebManagePermission::WARPS_EDIT,
        ]);

        $viewHomes = Permission::create([
            'name' => WebManagePermission::HOMES_VIEW,
        ]);

        $editHomes = Permission::create([
            'name' => WebManagePermission::HOMES_EDIT,
        ]);

        $viewServers = Permission::create([
            'name' => WebManagePermission::SERVERS_VIEW,
        ]);

        $editServers = Permission::create([
            'name' => WebManagePermission::SERVERS_EDIT,
        ]);

        $viewServerTokens = Permission::create([
            'name' => WebManagePermission::SERVER_TOKENS_VIEW,
        ]);

        $editServerTokens = Permission::create([
            'name' => WebManagePermission::SERVER_TOKENS_EDIT,
        ]);

        $editRemoteConfig = Permission::create([
            'name' => WebManagePermission::REMOTE_CONFIG_EDIT,
        ]);

        $viewDonations = Permission::create([
            'name' => WebManagePermission::DONATIONS_VIEW,
        ]);

        $editDonations = Permission::create([
            'name' => WebManagePermission::DONATIONS_EDIT,
        ]);

        $dev->permissions()->attach([
            $viewRoles,
            $editRoles,
            $assignRoles,
            $viewPermissions,
            $editPermissions,
            $assignPermissions,
            $viewAccounts,
            $editAccounts,
            $viewAccountEmails,
            $viewUUIDBans,
            $editUUIDBans,
            $viewIPBans,
            $editIPBans,
            $viewWarnings,
            $editWarnings,
            $viewPlayers,
            $editPlayers,
            $viewPlayerIps,
            $viewBadges,
            $editBadges,
            $viewWarps,
            $editWarps,
            $viewHomes,
            $editHomes,
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
            $viewHomes,
            $editHomes,
        ]);

        $architect->permissions()->attach([

        ]);
    }
}
