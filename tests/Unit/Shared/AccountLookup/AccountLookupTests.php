<?php

namespace Tests\Unit\Shared\AccountLookup;

use App\Entities\Models\Eloquent\Account;
use App\Entities\Models\Eloquent\MinecraftPlayer;
use App\Entities\Models\GameIdentifierType;
use App\Entities\Repositories\MinecraftPlayerRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Shared\AccountLookup\AccountLookup;
use Shared\AccountLookup\Exceptions\NoLinkedAccountException;
use Shared\AccountLookup\Exceptions\PlayerNotFoundException;
use Tests\TestCase;

class AccountLookupTests extends TestCase
{
    use RefreshDatabase;

    private MinecraftPlayerRepository $minecraftPlayerRepository;
    private AccountLookup $accountLookup;

    public function setUp(): void
    {
        parent::setUp();

        $this->minecraftPlayerRepository = \Mockery::mock(MinecraftPlayerRepository::class);

        $this->accountLookup = new AccountLookup(
            minecraftPlayerRepository: $this->minecraftPlayerRepository,
        );
    }

    public function test_throws_exception_if_no_player()
    {
        $uuid = 'uuid';

        $this->minecraftPlayerRepository
            ->shouldReceive('getByUuid')
            ->with($uuid)
            ->andReturnNull();

        $this->expectException(PlayerNotFoundException::class);

        $this->accountLookup->find(
            identifier: $uuid,
            identifierType: GameIdentifierType::MINECRAFT_UUID,
        );
    }

    public function test_throws_exception_if_no_linked_account()
    {
        $uuid = 'uuid';
        $player = MinecraftPlayer::factory()->create();

        $this->minecraftPlayerRepository
            ->shouldReceive('getByUuid')
            ->with($uuid)
            ->andReturn($player);

        $this->expectException(NoLinkedAccountException::class);

        $this->accountLookup->find(
            identifier: $uuid,
            identifierType: GameIdentifierType::MINECRAFT_UUID,
        );
    }

    public function test_gets_account_for_minecraft_player()
    {
        $uuid = 'uuid';
        $account = Account::factory()->create();
        $player = MinecraftPlayer::factory()
            ->for($account)
            ->create();

        $this->minecraftPlayerRepository
            ->shouldReceive('getByUuid')
            ->with($uuid)
            ->andReturn($player);

        $actual = $this->accountLookup->find(
            identifier: $uuid,
            identifierType: GameIdentifierType::MINECRAFT_UUID,
        );

        $this->assertEquals(
            expected: $account->getKey(),
            actual: $actual->getKey(),
        );
    }
}
