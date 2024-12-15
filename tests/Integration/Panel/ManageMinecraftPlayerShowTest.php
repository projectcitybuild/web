<?php

namespace Tests\Integration\Panel;

use App\Models\Account;
use App\Models\GamePlayerBan;
use App\Models\MinecraftPlayer;
use Tests\TestCase;

class ManageMinecraftPlayerShowTest extends TestCase
{
    public function test_ban_shown()
    {
        $banningStaff = MinecraftPlayer::factory()->create();
        $bannedPlayer = MinecraftPlayer::factory()
            ->has(GamePlayerBan::factory()->bannedBy($banningStaff))
            ->create();

        $this->actingAs($this->adminAccount())
            ->get(route('manage.minecraft-players.show', $bannedPlayer))
            ->assertOk()
            ->assertSee(GamePlayerBan::first()->reason);
    }

    public function test_ban_with_null_staff_shown()
    {
        $bannedPlayer = MinecraftPlayer::factory()
            ->has(GamePlayerBan::factory()->bannedByConsole())
            ->create();

        $this->actingAs($this->adminAccount())
            ->get(route('manage.minecraft-players.show', $bannedPlayer))
            ->assertOk()
            ->assertSee(GamePlayerBan::first()->reason);
    }

    public function test_unauthorised_without_scope()
    {
        $bannedPlayer = MinecraftPlayer::factory()
            ->has(GamePlayerBan::factory()->bannedByConsole())
            ->create();

        $this->actingAs($this->adminAccount())
            ->get(route('manage.minecraft-players.show', $bannedPlayer))
            ->assertSuccessful();

        $this->actingAs($this->staffAccount())
            ->get(route('manage.minecraft-players.show', $bannedPlayer))
            ->assertForbidden();

        $this->actingAs(Account::factory()->create())
            ->get(route('manage.minecraft-players.show', $bannedPlayer))
            ->assertForbidden();
    }
}
