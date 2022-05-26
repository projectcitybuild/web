<?php

namespace Feature;

use Entities\Models\Eloquent\Account;
use Entities\Models\Eloquent\GameBan;
use Entities\Models\Eloquent\MinecraftPlayer;
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

    public function test_shows_users_minecraft_accounts()
    {
        $player = $this->createPlayerWithAccountAndLogin();
        $this->get(route('front.appeal'))
            ->assertSee($player->uuid);
    }

    public function test_shows_if_account_is_banned()
    {
        $player = $this->createPlayerWithAccountAndLogin();
        GameBan::factory()->for($player, 'bannedPlayer')->active()->create();
        $this->get(route('front.appeal'))
            ->assertSeeTextInOrder([$player->uuid, 'Banned', 'Appeal']);
    }

    public function test_shows_if_appeal_open()
    {
        $player = $this->createPlayerWithAccountAndLogin();
        GameBan::factory()->for($player, 'bannedPlayer')->hasBanAppeals(1)->active()->create();
        $this->get(route('front.appeal'))
            ->assertSeeTextInOrder([$player->uuid, 'Appealing', 'View']);
    }
}
