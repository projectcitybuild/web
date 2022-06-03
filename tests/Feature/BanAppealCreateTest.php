<?php

namespace Tests\Feature;


use Entities\Models\Eloquent\Account;
use Entities\Models\Eloquent\BanAppeal;
use Entities\Models\Eloquent\GameBan;
use Entities\Models\Eloquent\MinecraftPlayer;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class BanAppealCreateTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    private function createBan()
    {
        return GameBan::factory()
            ->active()
            ->for(MinecraftPlayer::factory(), 'bannedPlayer')
            ->create([
                'reason' => 'Some Ban Reason'
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

    public function testCreatePageNotFoundForInactiveBans()
    {
        $ban = GameBan::factory()->inactive()->for(MinecraftPlayer::factory(), 'bannedPlayer')->create();
        $this->get(route('front.appeal.create', $ban))
            ->assertNotFound();
    }

    public function testCreatePageShowsForPlayerWithBans()
    {
        $this->get(route('front.appeal.create', $this->createBan()))
            ->assertSee('Appeal Form');
    }

    public function testCreatePageShowsActiveBanDetails()
    {
        $this->get(route('front.appeal.create', $this->createBan()))
            ->assertSee('Some Ban Reason');
    }

    public function testCreatePageShowsPastBanDetails()
    {
        $ban = $this->createBan();
        GameBan::factory()->inactive()->for($ban->bannedPlayer, 'bannedPlayer')->create(['reason' => 'My Expired Ban']);
        $this->get(route('front.appeal.create', $ban))
            ->assertSee('My Expired Ban');
    }

    public function testPendingAppealRedirectsToAppealWithAccount()
    {
        $ban = $this->createBanWithAccount();
        BanAppeal::factory()->for($ban)->create();
        $this->get(route('front.appeal.create', $ban))
            ->assertSee('appeal in progress');
    }

    public function testPendingAppealShowsNoticeForAppealWithoutAccount()
    {
        $ban = $this->createBan();
        BanAppeal::factory()->for($ban)->create();
        $this->get(route('front.appeal.create', $ban))
            ->assertSee('Click the link in the appeal confirmation email');
    }

    private function submitAppealForBan(GameBan $ban, bool $withEmail = false): TestResponse
    {
        $data = [
            "explanation" => "My Ban Appeal"
        ];

        if ($withEmail) {
            $data['email'] = 'test@example.org';
        }

        return $this->post(route('front.appeal.submit', $ban), $data);
    }

    public function testCanSubmitAppeal()
    {
        $ban = $this->createBanWithAccount(true);

        $this->submitAppealForBan($ban)
            ->assertSessionHasNoErrors()
            ->assertRedirect();
    }

    public function testRedirectsToUnsignedUrlIfAccountMatchesPlayerOwner()
    {
        $ban = $this->createBanWithAccount(true);
        $this->submitAppealForBan($ban)
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('front.appeal.show', BanAppeal::first()));
    }

    public function testRedirectsToSignedUrlIfUserNotSignedIn()
    {
        $ban = $this->createBanWithAccount();
        $this->submitAppealForBan($ban, true)
            ->assertSessionHasNoErrors()
            ->assertRedirectToSignedRoute('front.appeal.show', BanAppeal::first());
    }

    public function testRedirectsToSignedUrlIfSignedInUserDifferent()
    {
        $ban = $this->createBanWithAccount();
        $this->actingAs(Account::factory()->create());
        $this->submitAppealForBan($ban, true)
            ->assertSessionHasNoErrors()
            ->assertRedirectToSignedRoute('front.appeal.show', BanAppeal::first());
    }
}
