<?php

namespace Unit\Domain\Balances\UseCases;

use Domain\Balances\Exceptions\InsufficientBalanceException;
use Domain\Balances\UseCases\DeductBalanceUseCase;
use Entities\Models\Eloquent\Account;
use Entities\Models\Eloquent\MinecraftPlayer;
use Repositories\BalanceHistoryRepository;
use Shared\PlayerLookup\Entities\PlayerIdentifier;
use Shared\PlayerLookup\PlayerLookup;
use Tests\TestCase;

class DeductBalanceUseCaseTest extends TestCase
{
    private PlayerLookup $playerLookup;
    private BalanceHistoryRepository $balanceHistoryRepository;
    private DeductBalanceUseCase $useCase;

    public function setUp(): void
    {
        parent::setUp();

        $this->playerLookup = \Mockery::mock(PlayerLookup::class);
        $this->balanceHistoryRepository = \Mockery::mock(BalanceHistoryRepository::class);

        $this->useCase = new DeductBalanceUseCase(
            playerLookup: $this->playerLookup,
            balanceHistoryRepository: $this->balanceHistoryRepository,
        );
    }

    public function test_throws_exception_if_amount_equals_0()
    {
        $this->expectException(\Exception::class);

        $this->useCase->execute(
            identifier: PlayerIdentifier::minecraftUUID('uuid'),
            amount: 0,
            reason: 'reason',
        );
    }

    public function test_throws_exception_if_amount_less_than_1()
    {
        $this->expectException(\Exception::class);

        $this->useCase->execute(
            identifier: PlayerIdentifier::minecraftUUID('uuid'),
            amount: -1,
            reason: 'reason',
        );
    }

    public function test_throws_exception_if_insufficient_balance()
    {
        $balance = 10;
        $account = Account::factory()->create(['balance' => $balance]);
        $player = MinecraftPlayer::factory()->for($account)->create();
        $identifier = PlayerIdentifier::minecraftUUID('uuid');

        $this->playerLookup
            ->shouldReceive('find')
            ->with($identifier)
            ->andReturn($player);

        $this->expectException(InsufficientBalanceException::class);

        $this->useCase->execute(
            identifier: $identifier,
            amount: $balance + 1,
            reason: 'reason',
        );
    }

    public function test_deducts_from_balance()
    {
        $account = Account::factory()->create(['balance' => 10]);
        $player = MinecraftPlayer::factory()->for($account)->create();
        $identifier = PlayerIdentifier::minecraftUUID('uuid');

        $this->playerLookup
            ->shouldReceive('find')
            ->with($identifier)
            ->andReturn($player);

        $this->balanceHistoryRepository
            ->shouldReceive('create')
            ->with($account->getKey(), 10, 7, -3, 'reason');

        $this->useCase->execute(
            identifier: $identifier,
            amount: 3,
            reason: 'reason',
        );

        $this->assertEquals(
            expected: 7,
            actual: Account::find($account->getKey())->balance,
        );
    }
}
