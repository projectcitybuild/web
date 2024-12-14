<?php

namespace Panel;

use App\Domains\BanAppeals\Entities\BanAppealStatus;
use App\Domains\BanAppeals\Notifications\BanAppealUpdatedNotification;
use App\Domains\Bans\Data\UnbanType;
use App\Domains\Manage\Data\PanelGroupScope;
use App\Models\Account;
use App\Models\BanAppeal;
use App\Models\GamePlayerBan;
use App\Models\MinecraftPlayer;
use Illuminate\Support\Facades\Notification;
use Tests\IntegrationTestCase;

class PanelBanAppealDecisionTest extends IntegrationTestCase
{
    private BanAppeal $appeal;
    private GamePlayerBan $gamePlayerBan;
    private Account $admin;

    protected function setUp(): void
    {
        parent::setUp();
        Notification::fake();
        $this->gamePlayerBan = GamePlayerBan::factory()->for(MinecraftPlayer::factory(), 'bannedPlayer')->create();
        $this->appeal = BanAppeal::factory()->for($this->gamePlayerBan)->create(['explanation' => 'My Explanation']);

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
            ->put(route('manage.ban-appeals.update', $this->appeal), [
                'decision_note' => 'Some Note',
                'status' => BanAppealStatus::ACCEPTED_UNBAN->value,
            ])
            ->assertSessionHasErrors();
        Notification::assertNothingSent();
    }

    public function test_can_unban_player()
    {
        $this->actingAs($this->admin)
            ->put(route('manage.ban-appeals.update', $this->appeal), [
                'decision_note' => 'Some Note',
                'status' => BanAppealStatus::ACCEPTED_UNBAN->value,
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('manage.ban-appeals.show', $this->appeal));
        $this->assertEquals(BanAppealStatus::ACCEPTED_UNBAN, $this->appeal->refresh()->status);
        $this->assertEquals(false, $this->appeal->gamePlayerBan->refresh()->is_active);
        $this->assertDatabaseHas(GamePlayerBan::tableName(), [
            'id' => $this->gamePlayerBan->getKey(),
            'unbanned_at' => now(),
            'unbanner_player_id' => $this->admin->minecraftAccount()->first()->getKey(),
            'unban_type' => UnbanType::APPEALED,
        ]);
        Notification::assertSentTo($this->appeal, BanAppealUpdatedNotification::class);
    }

    public function test_can_deny_appeal()
    {
        $this->actingAs($this->admin)
            ->put(route('manage.ban-appeals.update', $this->appeal), [
                'decision_note' => 'Some Note',
                'status' => BanAppealStatus::DENIED->value,
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('manage.ban-appeals.show', $this->appeal));
        $this->assertEquals(BanAppealStatus::DENIED, $this->appeal->refresh()->status);
        $this->assertDatabaseHas(GamePlayerBan::tableName(), [
            'id' => $this->gamePlayerBan->getKey(),
            'unbanned_at' => null,
            'unbanner_player_id' => null,
            'unban_type' => null,
        ]);
        Notification::assertSentTo($this->appeal, BanAppealUpdatedNotification::class);
    }

    /*
     * The ban was made inactive, but not via the appeal.
     * E.g. the player was unbanned in game
     */
    public function test_validation_error_if_ban_has_become_inactive()
    {
        $this->gamePlayerBan->unbanned_at = now()->subDay();
        $this->gamePlayerBan->save();

        $this->actingAs($this->admin)
            ->put(route('manage.ban-appeals.update', $this->appeal), [
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
            ->put(route('manage.ban-appeals.update', $this->appeal), [
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
            ->put(route('manage.ban-appeals.update', $this->appeal))
            ->assertUnauthorized();
    }

    public function test_unauthorised_without_panel_access()
    {
        $admin = $this->adminAccount(scopes: [
            PanelGroupScope::REVIEW_APPEALS,
        ]);

        $this->actingAs($admin)
            ->put(route('manage.ban-appeals.update', $this->appeal))
            ->assertUnauthorized();
    }
}
