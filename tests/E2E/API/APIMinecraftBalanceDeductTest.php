<?php

namespace Tests\E2E\API;

use Domain\ServerTokens\ScopeKey;
use Entities\Models\Eloquent\Account;
use Entities\Models\Eloquent\MinecraftPlayer;
use Entities\Models\Eloquent\Server;
use Entities\Models\Eloquent\ServerCategory;
use Entities\Models\Eloquent\ServerToken;
use Entities\Models\Eloquent\ServerTokenScope;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class APIMinecraftBalanceDeductTest extends TestCase
{
    use RefreshDatabase;

    private ServerToken $token;

    protected function setUp(): void
    {
        parent::setUp();

        $serverCategory = ServerCategory::create(['name' => '_' ,'display_order' => 0]);
        $server = Server::factory()->create(['server_category_id' => $serverCategory->getKey()]);
        $this->token = ServerToken::factory()->create(['server_id' => $server->getKey()]);
    }

    private function endpoint(?MinecraftPlayer $player): string
    {
        $uuid = $player?->uuid ?? 'invalid';

        return 'api/v2/minecraft/'.$uuid.'/balance/deduct';
    }

    private function authorise(ScopeKey ...$scopes)
    {
        foreach ($scopes as $scope) {
            $tokenScope = ServerTokenScope::create(['scope' => $scope->value]);
            $this->token->scopes()->attach($tokenScope->getKey());
        }
    }

    public function test_requires_scope()
    {
        $account = Account::factory()->create(['balance' => 100]);
        $player = MinecraftPlayer::factory()
            ->for($account)
            ->create();

        $this->withHeader('Authorization', 'Bearer '.$this->token->token)
            ->postJson($this->endpoint($player))
            ->assertUnauthorized();

        $this->authorise(scope: ScopeKey::ACCOUNT_BALANCE_DEDUCT);

        $this->withHeader('Authorization', 'Bearer '.$this->token->token)
            ->postJson(
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

        $this->withHeader('Authorization', 'Bearer '.$this->token->token)
            ->postJson($this->endpoint($player))
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

        $this->withHeader('Authorization', 'Bearer '.$this->token->token)
            ->postJson(
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

        $this->withHeader('Authorization', 'Bearer '.$this->token->token)
            ->postJson(
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
