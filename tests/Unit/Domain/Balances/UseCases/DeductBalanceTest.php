<?php

namespace Tests\Unit\Domain\Balances\UseCases;

use App\Core\Domains\PlayerLookup\Entities\PlayerIdentifier;
use App\Core\Domains\PlayerLookup\Service\PlayerLookup;
use App\Core\Domains\PlayerLookup\Service\PlayerLookupMock;
use App\Domains\Balances\Exceptions\InsufficientBalanceException;
use App\Domains\Balances\UseCases\DeductBalance;
use App\Models\Account;
use App\Models\MinecraftPlayer;
use Repositories\BalanceHistoryRepository;
use Tests\TestCase;

class DeductBalanceTest extends TestCase
{
    private PlayerLookup $playerLookup;
    private BalanceHistoryRepository $balanceHistoryRepository;
    private DeductBalance $useCase;

    public function setUp(): void
    {
        parent::setUp();

        $this->playerLookup = new PlayerLookupMock();
        $this->balanceHistoryRepository = \Mockery::mock(BalanceHistoryRepository::class);

        $this->useCase = new DeductBalance(
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

        $this->playerLookup->find = $player;

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

        $this->playerLookup->find = $player;

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
