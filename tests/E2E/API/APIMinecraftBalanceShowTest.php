<?php

namespace Tests\E2E\API;

use Domain\ServerTokens\ScopeKey;
use Entities\Models\Eloquent\Account;
use Entities\Models\Eloquent\MinecraftPlayer;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use function collect;

class APIMinecraftBalanceShowTest extends TestCase
{
    private function endpoint(?MinecraftPlayer $player): string
    {
        $uuid = $player?->uuid ?? 'invalid';

        return 'api/v2/minecraft/'.$uuid.'/balance';
    }

    private function authorise(ScopeKey ...$scope)
    {
        Sanctum::actingAs(
            user: Account::factory()->create(),
            abilities: collect($scope)
                ->map(fn ($s) => $s->value)
                ->toArray(),
        );
    }

    public function test_requires_scope()
    {
        $account = Account::factory()->create();
        $player = MinecraftPlayer::factory()
            ->for($account)
            ->create();

        $this->getJson($this->endpoint($player))
            ->assertUnauthorized();

        $this->authorise(scope: ScopeKey::ACCOUNT_BALANCE_SHOW);

        $this->getJson($this->endpoint($player))
            ->assertOk();
    }

    public function test_shows_balance()
    {
        $account = Account::factory()->create(['balance' => 150]);
        $player = MinecraftPlayer::factory()
            ->for($account)
            ->create();

        $this->authorise(scope: ScopeKey::ACCOUNT_BALANCE_SHOW);

        $this->getJson($this->endpoint($player))
            ->assertJson([
                'data' => [
                    'balance' => 150,
                ],
            ]);
    }

    public function test_shows_error_without_player()
    {
        $this->authorise(scope: ScopeKey::ACCOUNT_BALANCE_SHOW);

        $this->getJson($this->endpoint(null))
            ->assertJson([
                'error' => [
                    'id' => 'player_not_found',
                    'detail' => 'Cannot find this player',
                    'status' => 404,
                ],
            ]);
    }

    public function test_shows_error_without_linked_account()
    {
        $player = MinecraftPlayer::factory()->create();

        $this->authorise(scope: ScopeKey::ACCOUNT_BALANCE_SHOW);

        $this->getJson($this->endpoint($player))
            ->assertJson([
                'error' => [
                    'id' => 'no_linked_account',
                    'detail' => 'Player is not linked to a PCB account',
                    'status' => 404,
                ],
            ]);
    }
}
