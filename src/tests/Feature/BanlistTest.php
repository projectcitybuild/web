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
    }

    public function makeBan($args = [])
    {
        return factory(GameBan::class)->create($args);

    }

    public function testActiveBanIsShownOnList()
    {
        $ban = $this->makeBan();
        $this->get(route('front.banlist'))
            ->assertSee($ban->reason);
    }
}
