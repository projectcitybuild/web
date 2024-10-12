<?php

namespace Front;

use App\Models\Account;
use App\Models\BanAppeal;
use App\Models\GamePlayerBan;
use App\Models\MinecraftPlayer;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class BanAppealViewTest extends TestCase
{
    private BanAppeal $banAppeal;

    protected function setUp(): void
    {
        parent::setUp();
        $this->banAppeal = BanAppeal::factory()
            ->for(GamePlayerBan::factory()->for(MinecraftPlayer::factory(), 'bannedPlayer'))
            ->create();
    }

    public function test_can_view_ban_appeal_signed()
    {
        $signedUrl = URL::signedRoute('front.appeal.show', ['banAppeal' => $this->banAppeal]);
        $this->get($signedUrl)
            ->assertOk();
    }

    public function test_cant_view_ban_appeal_with_wrong_signature()
    {
        $this->get(route('front.appeal.show', ['banAppeal' => $this->banAppeal, 'signature' => 'foo']))
            ->assertForbidden();
    }

    public function test_can_view_if_player_owner_and_unsigned()
    {
        $account = Account::factory()->create();
        $this->banAppeal->gamePlayerBan->bannedPlayer->account()->associate($account)->save();
        $this->actingAs($account)
            ->get(route('front.appeal.show', $this->banAppeal))
            ->assertOk();
    }

    public function test_cant_view_if_not_player_owner_and_unsigned()
    {
        $this->banAppeal->gamePlayerBan->bannedPlayer->account()->associate(Account::factory()->create())->save();
        $this->actingAs(Account::factory()->create())
            ->get(route('front.appeal.show', $this->banAppeal))
            ->assertForbidden();
    }
}
