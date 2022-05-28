<?php

namespace Tests\E2E\API;

use Domain\ServerTokens\ScopeKey;
use Entities\Models\Eloquent\Account;
use Entities\Models\Eloquent\MinecraftPlayer;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use function collect;

class APIMinecraftBalanceDeductTest extends TestCase
{
    private function endpoint(?MinecraftPlayer $player): string
    {
        $uuid = $player?->uuid ?? 'invalid';

        return 'api/v2/minecraft/'.$uuid.'/balance/deduct';
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
        $account = Account::factory()->create(['balance' => 100]);
        $player = MinecraftPlayer::factory()
            ->for($account)
            ->create();

        $this->postJson($this->endpoint($player))
            ->assertUnauthorized();

        $this->authorise(scope: ScopeKey::ACCOUNT_BALANCE_DEDUCT);

        $this->postJson(
            uri: $this->endpoint($player),
            data: [
                'amount' => 1,
                'reason' => 'test',
            ])
            ->assertOk();
    }

    public function test_cannot_deduct_without_input()
    {
        $account = Account::factory()->create(['balance' => 100]);
        $player = MinecraftPlayer::factory()
            ->for($account)
            ->create();

        $this->authorise(scope: ScopeKey::ACCOUNT_BALANCE_DEDUCT);

        $this->postJson($this->endpoint($player))
            ->assertJson([
                'error' => [
                    'id' => 'bad_input',
                    'detail' => 'The amount field is required.',
                    'status' => 400,
                ],
            ])
            ->assertStatus(400);
    }

    public function test_cannot_deduct_more_than_balance()
    {
        $account = Account::factory()->create(['balance' => 100]);
        $player = MinecraftPlayer::factory()
            ->for($account)
            ->create();

        $this->authorise(scope: ScopeKey::ACCOUNT_BALANCE_DEDUCT);

        $this->postJson(
            uri: $this->endpoint($player),
            data: [
                'amount' => 200,
                'reason' => 'test',
            ])
            ->assertJson([
                'error' => [
                    'id' => 'insufficient_balance',
                    'detail' => 'Cannot deduct more than the player\'s balance',
                    'status' => 400,
                ],
            ])
            ->assertStatus(400);
    }

    public function test_deducts_from_balance()
    {
        $now = Carbon::create(year: 2022, month: 4, day: 17, hour: 10, minute: 9, second: 8);
        Carbon::setTestNow($now);

        $account = Account::factory()->create(['balance' => 100]);
        $player = MinecraftPlayer::factory()
            ->for($account)
            ->create();

        $this->authorise(scope: ScopeKey::ACCOUNT_BALANCE_DEDUCT);

        $this->postJson(
            uri: $this->endpoint($player),
            data: [
                'amount' => 25,
                'reason' => 'test',
            ])
            ->assertOk();

        $this->assertDatabaseHas(
            table: 'accounts',
            data: [
                'account_id' => $account->getKey(),
                'balance' => 75,
            ],
        );

        $this->assertDatabaseHas(
            table: 'account_balance_transactions',
            data: [
                'account_id' => $account->getKey(),
                'balance_before' => 100,
                'balance_after' => 75,
                'transaction_amount' => -25,
                'reason' => 'test',
                'created_at' => $now,
            ],
        );
    }
}
