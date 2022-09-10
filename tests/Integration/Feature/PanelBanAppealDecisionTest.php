<?php

namespace Tests\Integration\Feature;

use Domain\BanAppeals\Entities\BanAppealStatus;
use Entities\Models\Eloquent\Account;
use Entities\Models\Eloquent\BanAppeal;
use Entities\Models\Eloquent\GameBan;
use Entities\Models\Eloquent\MinecraftPlayer;
use Entities\Models\PanelGroupScope;
use Entities\Notifications\BanAppealUpdatedNotification;
use Illuminate\Support\Facades\Notification;
use Tests\IntegrationTestCase;

class PanelBanAppealDecisionTest extends IntegrationTestCase
{
    private BanAppeal $appeal;
    private GameBan $gameBan;
    private Account $admin;

    protected function setUp(): void
    {
        parent::setUp();
        Notification::fake();
        $this->gameBan = GameBan::factory()->for(MinecraftPlayer::factory(), 'bannedPlayer')->create();
        $this->appeal = BanAppeal::factory()->for($this->gameBan)->create(['explanation' => 'My Explanation']);

        $this->admin = $this->adminAccount(scopes: [
            PanelGroupScope::ACCESS_PANEL,
            PanelGroupScope::REVIEW_APPEALS,
        ]);

        MinecraftPlayer::factory()->for($this->admin, 'account')->create();
    }

    public function test_error_if_account_has_no_players()
    {
        $admin = $this->adminAccount(scopes: [
            PanelGroupScope::ACCESS_PANEL,
            PanelGroupScope::REVIEW_APPEALS,
        ]);

        $this->actingAs($admin)
            ->put(route('front.panel.ban-appeals.update', $this->appeal), [
                'decision_note' => 'Some Note',
                'status' => BanAppealStatus::ACCEPTED_UNBAN->value,
            ])
            ->assertSessionHasErrors();
        Notification::assertNothingSent();
    }

    public function test_can_unban_player()
    {
        $this->actingAs($this->admin)
            ->put(route('front.panel.ban-appeals.update', $this->appeal), [
                'decision_note' => 'Some Note',
                'status' => BanAppealStatus::ACCEPTED_UNBAN->value,
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('front.panel.ban-appeals.show', $this->appeal));
        $this->assertEquals(BanAppealStatus::ACCEPTED_UNBAN, $this->appeal->refresh()->status);
        $this->assertEquals(false, $this->appeal->gameBan->refresh()->is_active);
        $this->assertDatabaseHas('game_network_unbans', [
            'game_ban_id' => $this->gameBan->getKey(),
            'staff_player_id' => $this->admin->minecraftAccount()->first()->getKey(),
        ]);
        Notification::assertSentTo($this->appeal, BanAppealUpdatedNotification::class);
    }

    public function test_can_deny_appeal()
    {
        $this->actingAs($this->admin)
            ->put(route('front.panel.ban-appeals.update', $this->appeal), [
                'decision_note' => 'Some Note',
                'status' => BanAppealStatus::DENIED->value,
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('front.panel.ban-appeals.show', $this->appeal));
        $this->assertEquals(BanAppealStatus::DENIED, $this->appeal->refresh()->status);
        $this->assertEquals(true, $this->appeal->gameBan->refresh()->is_active);
        $this->assertDatabaseMissing('game_network_unbans', [
            'game_ban_id' => $this->gameBan->getKey(),
        ]);
        Notification::assertSentTo($this->appeal, BanAppealUpdatedNotification::class);
    }

    /*
     * The ban was made inactive, but not via the appeal.
     * E.g. the player was unbanned in game
     */
    public function test_validation_error_if_ban_has_become_inactive()
    {
        $this->gameBan->is_active = false;
        $this->gameBan->save();

        $this->actingAs($this->admin)
            ->put(route('front.panel.ban-appeals.update', $this->appeal), [
                'decision_note' => 'Some Note',
                'status' => BanAppealStatus::ACCEPTED_UNBAN->value,
            ])
            ->assertSessionHasErrors();

        Notification::assertNothingSent();
    }

    /**
     * The appeal was already decided.
     * E.g. someone else decided it whilst the user was looking at the appeal
     */
    public function test_validation_error_if_appeal_already_decided()
    {
        $this->appeal->status = BanAppealStatus::DENIED->value;
        $this->appeal->save();

        $this->actingAs($this->admin)
            ->put(route('front.panel.ban-appeals.update', $this->appeal), [
                'decision_note' => 'Some Note',
                'status' => BanAppealStatus::ACCEPTED_UNBAN->value,
            ])
            ->assertSessionHasErrors();

        Notification::assertNothingSent();
    }

    public function test_unauthorised_without_scope()
    {
        $admin = $this->adminAccount(scopes: [
            PanelGroupScope::ACCESS_PANEL,
        ]);

        $this->actingAs($admin)
            ->put(route('front.panel.ban-appeals.update', $this->appeal))
            ->assertUnauthorized();
    }

    public function test_unauthorised_without_panel_access()
    {
        $admin = $this->adminAccount(scopes: [
            PanelGroupScope::REVIEW_APPEALS,
        ]);

        $this->actingAs($admin)
            ->put(route('front.panel.ban-appeals.update', $this->appeal))
            ->assertUnauthorized();
    }
}
