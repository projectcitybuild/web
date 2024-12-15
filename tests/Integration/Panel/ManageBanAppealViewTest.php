<?php

namespace Tests\Integration\Panel;

use App\Models\Account;
use App\Models\BanAppeal;
use App\Models\GamePlayerBan;
use App\Models\MinecraftPlayer;
use Tests\TestCase;

class ManageBanAppealViewTest extends TestCase
{
    public function test_can_view_pending_appeal()
    {
        $appeal = BanAppeal::factory()
            ->for(
                GamePlayerBan::factory()->for(MinecraftPlayer::factory(), 'bannedPlayer')
            )->create(['explanation' => 'My Explanation']);

        $this->actingAs($this->adminAccount())
            ->get(route('manage.ban-appeals.show', $appeal))
            ->assertOk()
            ->assertSee('My Explanation')
            ->assertSee('Awaiting Decision');
    }

    public function test_can_view_unbanned_appeal()
    {
        $appeal = BanAppeal::factory()
            ->unbanned()
            ->for(
                GamePlayerBan::factory()->inactive()->for(MinecraftPlayer::factory(), 'bannedPlayer')
            )->create(['decision_note' => 'Some Decision Reason']);

        $this->actingAs($this->adminAccount())
            ->get(route('manage.ban-appeals.show', $appeal))
            ->assertOk()
            ->assertSee('Some Decision Reason')
            ->assertSee('Unbanned');
    }

    public function test_can_view_tempbanned_appeal()
    {
        $appeal = BanAppeal::factory()
            ->tempBanned()
            ->for(
                GamePlayerBan::factory()->temporary()->for(MinecraftPlayer::factory(), 'bannedPlayer')
            )->create(['decision_note' => 'Some Decision Reason']);

        $this->actingAs($this->adminAccount())
            ->get(route('manage.ban-appeals.show', $appeal))
            ->assertOk()
            ->assertSee('Some Decision Reason')
            ->assertSee('Banned for');
    }

    public function test_can_view_denied_appeal()
    {
        $appeal = BanAppeal::factory()
            ->denied()
            ->for(
                GamePlayerBan::factory()->temporary()->for(MinecraftPlayer::factory(), 'bannedPlayer')
            )->create(['decision_note' => 'Some Decision Reason']);

        $this->actingAs($this->adminAccount())
            ->get(route('manage.ban-appeals.show', $appeal))
            ->assertOk()
            ->assertSee('Some Decision Reason')
            ->assertSee('Denied');
    }

    public function test_forbidden()
    {
        $appeal = BanAppeal::factory()
            ->for(GamePlayerBan::factory()->for(MinecraftPlayer::factory(), 'bannedPlayer'))
            ->create(['explanation' => 'My Explanation']);

        $this->actingAs($this->adminAccount())
            ->get(route('manage.ban-appeals.show', $appeal))
            ->assertSuccessful();

        $this->actingAs($this->staffAccount())
            ->get(route('manage.ban-appeals.show', $appeal))
            ->assertSuccessful();

        $this->actingAs(Account::factory()->create())
            ->get(route('manage.ban-appeals.show', $appeal))
            ->assertForbidden();
    }
}
