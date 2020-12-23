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
    private MinecraftPlayerAlias $mcPlayerAlias;

    protected function setUp(): void
    {
        parent::setUp();

        $this->account = Account::factory()->create();

        $this->mcPlayerAlias = MinecraftPlayerAlias::factory()->create();

        $this->mcPlayer = MinecraftPlayer::factory()
            ->for($this->account)
            ->has($this->mcPlayerAlias)
            ->create();
    }

    public function testMinecraftAccountShownInList()
    {
        $this->actingAs($this->account);

        $this->get(route('front.account.games'))
            ->assertOk()
            ->assertSee($this->mcPlayer->uuid)
            ->assertSee($this->mcPlayerAlias->alias);
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

        $this->delete(route('front.account.games.delete', $this->mcPlayer->getKey()))
            ->assertForbidden();
    }
}
