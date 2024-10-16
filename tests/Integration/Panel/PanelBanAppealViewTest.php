<?php

namespace Panel;

use App\Domains\Panel\Data\PanelGroupScope;
use App\Models\Account;
use App\Models\BanAppeal;
use App\Models\GamePlayerBan;
use App\Models\MinecraftPlayer;
use Tests\IntegrationTestCase;

class PanelBanAppealViewTest extends IntegrationTestCase
{
    private Account $admin;

    public function setUp(): void
    {
        parent::setUp();

        $this->admin = $this->adminAccount(scopes: [
            PanelGroupScope::ACCESS_PANEL,
            PanelGroupScope::REVIEW_APPEALS,
        ]);
    }

    public function test_can_view_pending_appeal()
    {
        $appeal = BanAppeal::factory()
            ->for(
                GamePlayerBan::factory()->for(MinecraftPlayer::factory(), 'bannedPlayer')
            )->create(['explanation' => 'My Explanation']);

        $this->actingAs($this->admin)
            ->get(route('front.panel.ban-appeals.show', $appeal))
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

        $this->actingAs($this->admin)
            ->get(route('front.panel.ban-appeals.show', $appeal))
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

        $this->actingAs($this->admin)
            ->get(route('front.panel.ban-appeals.show', $appeal))
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

        $this->actingAs($this->admin)
            ->get(route('front.panel.ban-appeals.show', $appeal))
            ->assertOk()
            ->assertSee('Some Decision Reason')
            ->assertSee('Denied');
    }

    public function test_unauthorised_without_scope()
    {
        $appeal = BanAppeal::factory()
            ->for(GamePlayerBan::factory()->for(MinecraftPlayer::factory(), 'bannedPlayer'))
            ->create(['explanation' => 'My Explanation']);

        $admin = $this->adminAccount(scopes: [
            PanelGroupScope::ACCESS_PANEL,
        ]);

        $this->actingAs($admin)
            ->get(route('front.panel.ban-appeals.show', $appeal))
            ->assertUnauthorized();
    }

    public function test_unauthorised_without_panel_access()
    {
        $appeal = BanAppeal::factory()
            ->for(GamePlayerBan::factory()->for(MinecraftPlayer::factory(), 'bannedPlayer'))
            ->create(['explanation' => 'My Explanation']);

        $admin = $this->adminAccount(scopes: [
            PanelGroupScope::MANAGE_ACCOUNTS,
        ]);

        $this->actingAs($admin)
            ->get(route('front.panel.ban-appeals.show', $appeal))
            ->assertUnauthorized();
    }
}
