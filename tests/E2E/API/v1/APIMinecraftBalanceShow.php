<?php

namespace Tests\E2E\API\v1;

use App\Entities\Models\Eloquent\Account;
use App\Entities\Models\Eloquent\MinecraftPlayer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Library\APITokens\APITokenScope;
use Tests\TestCase;

class APIMinecraftBalanceShow extends TestCase
{
    use RefreshDatabase;

    private function endpoint(?MinecraftPlayer $player): string
    {
        $uuid = $player?->uuid ?? 'invalid';

        return 'api/v1/minecraft/'.$uuid.'/balance';
    }

    private function authorise(APITokenScope ...$scope)
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

        $this->authorise(scope: APITokenScope::ACCOUNT_BALANCE_SHOW);

        $this->getJson($this->endpoint($player))
            ->assertOk();
    }

    public function test_shows_balance()
    {
        $account = Account::factory()->create(['balance' => 150]);
        $player = MinecraftPlayer::factory()
            ->for($account)
            ->create();

        $this->authorise(scope: APITokenScope::ACCOUNT_BALANCE_SHOW);

        $this->getJson($this->endpoint($player))
            ->assertJson([
                'balance' => 150,
            ]);
    }

    public function test_shows_error_without_player()
    {
        $this->authorise(scope: APITokenScope::ACCOUNT_BALANCE_SHOW);

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

        $this->authorise(scope: APITokenScope::ACCOUNT_BALANCE_SHOW);

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