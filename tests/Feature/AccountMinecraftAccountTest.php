<?php

namespace Tests\Feature;

use Entities\Models\Eloquent\Account;
use Entities\Models\Eloquent\MinecraftPlayer;
use Entities\Models\Eloquent\MinecraftPlayerAlias;
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

        $this->mcPlayer = MinecraftPlayer::factory()
            ->for($this->account)
            ->create();

        $this->mcPlayerAlias = MinecraftPlayerAlias::factory()
            ->for($this->mcPlayer)
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

        $noAliasMcPlayer = MinecraftPlayer::factory()
            ->for($this->account)
            ->create();

        $this->get(route('front.account.games'))
            ->assertOk()
            ->assertSee($noAliasMcPlayer->uuid);
    }

    // This shouldn't ever happen, but test just in case
    public function testMinecraftAccountNeverSynced()
    {
        $this->actingAs($this->account);

        $neverSyncedMcPlayer = MinecraftPlayer::factory()
            ->neverSynced()
            ->for($this->account)
            ->create();

        $this->get(route('front.account.games'))
            ->assertOk()
            ->assertSee($neverSyncedMcPlayer->uuid)
            ->assertSee('Never');
    }

    public function testCanUnlinkOwnAccount()
    {
        $this->actingAs($this->account);

        $this->delete(route('front.account.games.delete', $this->mcPlayer->getKey()))
            ->assertRedirect();

        $this->assertDatabaseHas('players_minecraft', [
            'uuid' => $this->mcPlayer->uuid,
            'account_id' => null,
        ]);
    }

    public function testCannotUnlinkOthersAccount()
    {
        $this->actingAs(Account::factory()->create());

        $this->delete(route('front.account.games.delete', $this->mcPlayer->getKey()))
            ->assertForbidden();
    }
}
