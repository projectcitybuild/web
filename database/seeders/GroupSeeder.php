<?php

namespace Database\Seeders;

use App\Models\Group;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    public function run()
    {
        Group::factory()->create([
            'name' => 'member',
            'minecraft_name' => 'default',
            'minecraft_display_name' => '<gray>[M]</gray>',
            'minecraft_hover_text' => 'Member',
            'is_default' => true,
            'group_type' => 'trust',
            'display_priority' => 1,
            'additional_homes' => 3,
        ]);

        Group::factory()->create([
            'name' => 'trusted',
            'minecraft_name' => 'trusted',
            'minecraft_display_name' => '<blue>[T]</blue>',
            'minecraft_hover_text' => 'Trusted',
            'group_type' => 'trust',
            'display_priority' => 2,
        ]);

        Group::factory()->create([
            'name' => 'trusted+',
            'minecraft_name' => 'trusted_plus',
            'minecraft_display_name' => '<purple>[T+]</purple>',
            'minecraft_hover_text' => 'Trusted+',
            'group_type' => 'trust',
            'display_priority' => 3,
        ]);

        Group::factory()->create([
            'name' => 'intern',
            'minecraft_name' => 'intern',
            'minecraft_display_name' => '<dark_gray>[I]</dark_gray>',
            'minecraft_hover_text' => 'Intern',
            'group_type' => 'build',
            'display_priority' => 1,
            'additional_homes' => 1,
        ]);

        Group::factory()->create([
            'name' => 'builder',
            'minecraft_name' => 'builder',
            'minecraft_display_name' => '<dark_gray>[B]</dark_gray>',
            'minecraft_hover_text' => 'Builder',
            'group_type' => 'build',
            'display_priority' => 2,
            'additional_homes' => 3,
        ]);

        Group::factory()->create([
            'name' => 'planner',
            'minecraft_name' => 'planner',
            'minecraft_display_name' => '<dark_gray>[P]</dark_gray>',
            'minecraft_hover_text' => 'Planner',
            'group_type' => 'build',
            'display_priority' => 3,
            'additional_homes' => 5,
        ]);

        Group::factory()->create([
            'name' => 'engineer',
            'minecraft_name' => 'engineer',
            'minecraft_display_name' => '<dark_gray>[E]</dark_gray>',
            'minecraft_hover_text' => 'Engineer',
            'group_type' => 'build',
            'display_priority' => 4,
            'additional_homes' => 7,
        ]);

        $architect = Group::factory()->create([
            'name' => 'architect',
            'minecraft_name' => 'architect',
            'minecraft_display_name' => '<dark_gray>[A]</dark_gray>',
            'minecraft_hover_text' => 'Architect',
            'group_type' => 'build',
            'display_priority' => 5,
            'additional_homes' => 10,
        ]);

        Group::factory()->create([
            'name' => 'donator',
            'minecraft_name' => 'donator',
            'minecraft_display_name' => '<green>[$]</green>',
            'minecraft_hover_text' => 'Donator',
            'group_type' => 'donor',
            'display_priority' => 1,
        ]);

        Group::factory()->create([
            'name' => 'legacy donor',
            'minecraft_name' => 'legacy-donor',
            'minecraft_display_name' => '<green>[$]</green>',
            'minecraft_hover_text' => 'Donator (Legacy)',
            'group_type' => 'donor',
            'display_priority' => 2,
        ]);

        $mod = Group::factory()->create([
            'name' => 'moderator',
            'minecraft_name' => 'moderator',
            'minecraft_display_name' => '<red>[Staff]</red>',
            'minecraft_hover_text' => 'Moderator',
            'alias' => 'Mod',
            'group_type' => 'staff',
            'display_priority' => 1,
            'additional_homes' => 5,
        ]);

        $dev = Group::factory()->create([
            'name' => 'developer',
            'minecraft_name' => 'develop',
            'minecraft_display_name' => '<red>[Staff]</red>',
            'minecraft_hover_text' => 'Developer',
            'alias' => 'Dev',
            'is_admin' => true,
            'group_type' => 'staff',
            'display_priority' => 2,
        ]);
    }
}
