<?php

namespace Tests\Feature;

use Domain\BanAppeals\Entities\BanAppealStatus;
use Entities\Models\Eloquent\Account;
use Entities\Models\Eloquent\BanAppeal;
use Entities\Models\Eloquent\GameBan;
use Entities\Models\Eloquent\MinecraftPlayer;
use Entities\Models\GameIdentifierType;
use Entities\Notifications\BanAppealUpdatedNotification;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PanelBanAppealDecisionTest extends TestCase
{
    private BanAppeal $appeal;
    private Account $account;
    private GameBan $gameBan;

    protected function setUp(): void
    {
        parent::setUp();
        Notification::fake();
        $this->gameBan = GameBan::factory()->active()->for(MinecraftPlayer::factory(), 'bannedPlayer')->create();
        $this->appeal = BanAppeal::factory()->for($this->gameBan)->create(['explanation' => 'My Explanation']);

        $this->account = $this->adminAccount();
        $staffMcPlayer = MinecraftPlayer::factory()->for($this->account, 'account')->create();
    }

    public function test_error_if_account_has_no_players()
    {
        $this->actingAs($this->adminAccount(true))
            ->put(route('front.panel.ban-appeals.update', $this->appeal), [
                'decision_note' => 'Some Note',
                'status' => BanAppealStatus::ACCEPTED_UNBAN->value
            ])
            ->assertSessionHasErrors();
        Notification::assertNothingSent();
    }

    public function test_can_unban_player()
    {
        $this->actingAs($this->account)
            ->put(route('front.panel.ban-appeals.update', $this->appeal), [
                'decision_note' => 'Some Note',
                'status' => BanAppealStatus::ACCEPTED_UNBAN->value
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('front.panel.ban-appeals.show', $this->appeal));
        $this->assertEquals(BanAppealStatus::ACCEPTED_UNBAN, $this->appeal->refresh()->status);
        $this->assertEquals(false, $this->appeal->gameBan->refresh()->is_active);
        $this->assertDatabaseHas('game_network_unbans', [
            'game_ban_id' => $this->gameBan->getKey(),
            'staff_player_id' => $this->account->minecraftAccount()->first()->getKey(),
            'staff_player_type' => GameIdentifierType::MINECRAFT_UUID->playerType()->value
        ]);
        Notification::assertSentTo($this->appeal, BanAppealUpdatedNotification::class);
    }

    public function test_can_deny_appeal()
    {
        $this->actingAs($this->account)
            ->put(route('front.panel.ban-appeals.update', $this->appeal), [
                'decision_note' => 'Some Note',
                'status' => BanAppealStatus::DENIED->value
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('front.panel.ban-appeals.show', $this->appeal));
        $this->assertEquals(BanAppealStatus::DENIED, $this->appeal->refresh()->status);
        $this->assertEquals(true, $this->appeal->gameBan->refresh()->is_active);
        $this->assertDatabaseMissing('game_network_unbans', [
            'game_ban_id' => $this->gameBan->getKey()
        ]);
        Notification::assertSentTo($this->appeal, BanAppealUpdatedNotification::class);
    }
}
