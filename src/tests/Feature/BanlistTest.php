<?php


namespace Tests\Feature;


use App\Entities\Accounts\Models\Account;
use App\Entities\Bans\Models\GameBan;
use App\Entities\Players\Models\MinecraftPlayer;
use App\Entities\Players\Models\MinecraftPlayerAlias;
use Tests\TestCase;

class BanlistTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->account = factory(Account::class)->create();
        $this->mcAccount = factory(MinecraftPlayer::class)->create([
            'account_id' => $this->account->account_id
        ]);
        $this->alias = factory(MinecraftPlayerAlias::class)->create([
            'player_minecraft_id' => $this->mcAccount->player_minecraft_id
        ]);
        $this->ban = factory(GameBan::class)->create();
    }

    public function testBanIsShownOnList()
    {
        $this->get(route('front.banlist'))
            ->assertSee($this->ban->reason);
    }
}
