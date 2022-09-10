<?php

namespace Tests\Integration\Feature;

use Entities\Models\Eloquent\Account;
use Entities\Models\Eloquent\BanAppeal;
use Entities\Models\Eloquent\GameBan;
use Entities\Models\Eloquent\MinecraftPlayer;
use Illuminate\Support\Facades\Notification;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class BanAppealCreateTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Notification::fake();
    }

    private function createBan()
    {
        return GameBan::factory()
            ->for(MinecraftPlayer::factory(), 'bannedPlayer')
            ->create([
                'reason' => 'Some Ban Reason',
            ]);
    }

    private function createBanWithAccount($login = false)
    {
        $ban = $this->createBan();
        $account = Account::factory()->create();
        $ban->bannedPlayer->account()->associate($account)->save();

        if ($login) {
            $this->actingAs($account);
        }

        return $ban;
    }

    private function submitAppealForBan(GameBan $ban, bool $withEmail = false): TestResponse
    {
        $data = [
            'explanation' => 'My Ban Appeal',
        ];

        if ($withEmail) {
            $data['email'] = 'test@example.org';
        }

        return $this->post(route('front.appeal.submit', $ban), $data);
    }

    public function test_create_page_not_found_for_inactive_bans()
    {
        $ban = GameBan::factory()->inactive()->for(MinecraftPlayer::factory(), 'bannedPlayer')->create();
        $this->get(route('front.appeal.create', $ban))
            ->assertNotFound();
    }

    public function test_create_page_shows_for_player_with_bans()
    {
        $this->get(route('front.appeal.create', $this->createBan()))
            ->assertSee('Appeal Form');
    }

    public function test_create_page_shows_active_ban_details()
    {
        $this->get(route('front.appeal.create', $this->createBan()))
            ->assertSee('Some Ban Reason');
    }

    public function test_create_page_shows_past_ban_details()
    {
        $ban = $this->createBan();
        GameBan::factory()->inactive()->for($ban->bannedPlayer, 'bannedPlayer')->create(['reason' => 'My Expired Ban']);
        $this->get(route('front.appeal.create', $ban))
            ->assertSee('My Expired Ban');
    }

    public function test_pending_appeal_redirects_to_appeal_with_account()
    {
        $ban = $this->createBanWithAccount();
        BanAppeal::factory()->for($ban)->create();
        $this->get(route('front.appeal.create', $ban))
            ->assertSee('appeal in progress');
    }

    public function test_pending_appeal_shows_notice_for_appeal_without_account()
    {
        $ban = $this->createBan();
        BanAppeal::factory()->for($ban)->create();
        $this->get(route('front.appeal.create', $ban))
            ->assertSee('Click the link in the appeal confirmation email');
    }

    public function test_can_submit_appeal()
    {
        $ban = $this->createBanWithAccount(true);

        $this->submitAppealForBan($ban)
            ->assertSessionHasNoErrors()
            ->assertRedirect();
    }

    public function test_redirects_to_unsigned_url_if_account_matches_player_owner()
    {
        $ban = $this->createBanWithAccount(true);
        $this->submitAppealForBan($ban)
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('front.appeal.show', BanAppeal::first()));
    }

    public function test_redirects_to_signed_url_if_user_not_signed_in()
    {
        $ban = $this->createBanWithAccount();
        $this->submitAppealForBan($ban, true)
            ->assertSessionHasNoErrors()
            ->assertRedirectToSignedRoute('front.appeal.show', BanAppeal::first());
    }

    public function test_redirects_to_signed_url_if_signed_in_user_different()
    {
        $ban = $this->createBanWithAccount();
        $this->actingAs(Account::factory()->create());
        $this->submitAppealForBan($ban, true)
            ->assertSessionHasNoErrors()
            ->assertRedirectToSignedRoute('front.appeal.show', BanAppeal::first());
    }
}
