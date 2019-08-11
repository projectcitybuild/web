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
        $this->ban = factory(GameBan::class)->create();
    }

    public function testBanIsShownOnList()
    {
        $this->get(route('front.banlist'))
            ->assertSee($this->ban->reason);
    }
}
