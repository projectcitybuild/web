<?php

namespace Tests\Feature;

use App\Entities\Accounts\Models\Account;
use App\Entities\Players\Models\MinecraftPlayer;
use App\Entities\Players\Models\MinecraftPlayerAlias;
use Tests\TestCase;

class AccountMinecraftAccountTest extends TestCase
{
    private Account $account;
    private MinecraftPlayer $mcPlayer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->account = factory(Account::class)->create();
        $this->mcPlayer = factory(MinecraftPlayer::class)->create(['account_id' => $this->account->getKey()]);
    }

    public function testMinecraftAccountShownInList()
    {
        $mcPlayerAlias = $this->withAlias();

        $this->actingAs($this->account);

        $this->get(route('front.account.games'))
            ->assertOk()
            ->assertSee($this->mcPlayer->uuid)
            ->assertSee($mcPlayerAlias->alias);
    }

    public function testMinecraftAccountWithoutAlias()
    {
        $this->actingAs($this->account);

        $this->get(route('front.account.games'))
            ->assertOk()
            ->assertSee($this->account->uuid);
    }

    public function testCanUnlinkOwnAccount()
    {
        $this->actingAs($this->account);
        $this->withAlias();

        $this->delete(route('front.account.games.delete', $this->mcPlayer->getKey()))
            ->assertRedirect();

        $this->assertDatabaseHas('players_minecraft', [
            'uuid' => $this->mcPlayer->uuid,
            'account_id' => null
        ]);
    }

    public function testCannotUnlinkOthersAccount()
    {
        $this->actingAs(factory(Account::class)->create());
        $this->withAlias();

        $this->delete(route('front.account.games.delete', $this->mcPlayer->getKey()))
            ->assertForbidden();
    }

    /**
     * Generate an alias for the minecraft player
     */
    private function withAlias()
    {
        return factory(MinecraftPlayerAlias::class)->create(['player_minecraft_id' => $this->mcPlayer->getKey()]);
    }
}
