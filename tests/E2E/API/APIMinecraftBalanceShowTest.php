<?php

namespace Tests\E2E\API;

use Domain\ServerTokens\ScopeKey;
use Entities\Models\Eloquent\Account;
use Entities\Models\Eloquent\MinecraftPlayer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\E2ETestCase;

class APIMinecraftBalanceShowTest extends E2ETestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->createServerToken();
    }

    private function endpoint(?MinecraftPlayer $player): string
    {
        $uuid = $player?->uuid ?? 'invalid';

        return 'api/v2/minecraft/'.$uuid.'/balance';
    }

    public function test_requires_scope()
    {
        $account = Account::factory()->create();
        $player = MinecraftPlayer::factory()
            ->for($account)
            ->create();

        $this->withAuthorizationServerToken()
            ->getJson($this->endpoint($player))
            ->assertForbidden();

        $this->authoriseTokenFor(ScopeKey::ACCOUNT_BALANCE_SHOW);

        $this->withAuthorizationServerToken()
            ->getJson($this->endpoint($player))
            ->assertOk();
    }

    public function test_shows_balance()
    {
        $account = Account::factory()->create(['balance' => 150]);
        $player = MinecraftPlayer::factory()
            ->for($account)
            ->create();

        $this->authoriseTokenFor(ScopeKey::ACCOUNT_BALANCE_SHOW);

        $this->withAuthorizationServerToken()
            ->getJson($this->endpoint($player))
            ->assertJson([
                'data' => [
                    'balance' => 150,
                ],
            ]);
    }

    public function test_missing_player_returns_zero_balance()
    {
        $this->authoriseTokenFor(ScopeKey::ACCOUNT_BALANCE_SHOW);

        $this->withAuthorizationServerToken()
            ->getJson($this->endpoint(null))
            ->assertJson([
                'data' => [
                    'balance' => 0,
                ],
            ]);
    }

    public function test_unlinked_account_returns_zero_balance()
    {
        $player = MinecraftPlayer::factory()->create();

        $this->authoriseTokenFor(ScopeKey::ACCOUNT_BALANCE_SHOW);

        $this->withAuthorizationServerToken()
            ->getJson($this->endpoint($player))
            ->assertJson([
                'data' => [
                    'balance' => 0,
                ],
            ]);
    }
}
