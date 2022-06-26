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

    public function test_minecraft_account_shown_in_list()
    {
        $this->actingAs($this->account);

        $this->get(route('front.account.games'))
            ->assertOk()
            ->assertSee($this->mcPlayer->uuid)
            ->assertSee($this->mcPlayerAlias->alias);
    }

    public function test_minecraft_account_without_alias()
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
    public function test_minecraft_account_never_synced()
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

    public function test_can_unlink_own_account()
    {
        $this->actingAs($this->account);

        $this->delete(route('front.account.games.delete', $this->mcPlayer->getKey()))
            ->assertRedirect();

        $this->assertDatabaseHas('players_minecraft', [
            'uuid' => $this->mcPlayer->uuid,
            'account_id' => null,
        ]);
    }

    public function test_cannot_unlink_others_account()
    {
        $this->actingAs(Account::factory()->create());

        $this->delete(route('front.account.games.delete', $this->mcPlayer->getKey()))
            ->assertForbidden();
    }
}
