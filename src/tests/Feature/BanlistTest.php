<?php


namespace Tests\Feature;


use App\Entities\Eloquent\Accounts\Models\Account;
use App\Entities\Eloquent\Bans\Models\GameBan;
use App\Entities\Eloquent\Players\Models\MinecraftPlayer;
use App\Entities\Eloquent\Players\Models\MinecraftPlayerAlias;
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
        $ban = $this->makeBan(['is_active' => 1]);
        $this->get(route('front.banlist'))
            ->assertSee($ban->reason);
    }

    public function testInactiveBanIsNotShownOnList()
    {
        $ban = $this->makeBan(['is_active' => 0]);
        $this->get(route('front.banlist'))
            ->assertDontSee($ban->reason);
    }
}
