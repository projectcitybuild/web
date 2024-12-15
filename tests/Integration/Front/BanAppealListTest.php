<?php

namespace Tests\Integration\Front;

use App\Domains\BanAppeals\Entities\BanAppealStatus;
use App\Models\Account;
use App\Models\GamePlayerBan;
use App\Models\MinecraftPlayer;
use Tests\TestCase;

class BanAppealListTest extends TestCase
{
    public function test_can_view_appeals_home_as_guest()
    {
        $this->get(route('front.appeal'))
            ->assertOk()
            ->assertSeeText('Sign in to Appeal')
            ->assertSeeText('Appeal as a Guest');
    }

    private function createPlayerWithAccountAndLogin()
    {
        $account = Account::factory()->create();
        $player = MinecraftPlayer::factory()->for($account)->create();
        $this->actingAs($account);

        return $player;
    }

    public function test_shows_ban()
    {
        $player = $this->createPlayerWithAccountAndLogin();

        $ban = GamePlayerBan::factory()
            ->for($player, 'bannedPlayer')
            ->create();

        $this->get(route('front.appeal'))
            ->assertSee($ban->reason);
    }

    public function test_shows_appeal()
    {
        $player = $this->createPlayerWithAccountAndLogin();

        GamePlayerBan::factory()
            ->for($player, 'bannedPlayer')
            ->hasBanAppeals(1)
            ->create();

        $this->get(route('front.appeal'))
            ->assertSee('Appeal #1');
    }

    public function test_shows_appeal_again()
    {
        $player = $this->createPlayerWithAccountAndLogin();

        GamePlayerBan::factory()
            ->for($player, 'bannedPlayer')
            ->hasBanAppeals(1, ['status' => BanAppealStatus::DENIED])
            ->create();

        $this->get(route('front.appeal'))
            ->assertSeeIgnoringWhitespace('Appeal Again');
    }

    public function test_shows_no_bans_message()
    {
        $this->createPlayerWithAccountAndLogin();
        $this->get(route('front.appeal'))
            ->assertSee('No Bans Found');
    }
}
