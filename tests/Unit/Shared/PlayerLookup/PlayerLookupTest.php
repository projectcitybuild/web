<?php

namespace Tests\Unit\Shared\PlayerLookup;

use Entities\Models\Eloquent\MinecraftPlayer;
use Entities\Models\Eloquent\MinecraftPlayerAlias;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Repositories\MinecraftPlayerAliasRepository;
use Shared\PlayerLookup\Entities\PlayerIdentifier;
use Shared\PlayerLookup\Exceptions\NonCreatableIdentifierException;
use Shared\PlayerLookup\PlayerLookup;
use Shared\PlayerLookup\Repositories\MinecraftPlayerRepository;
use Tests\TestCase;

class PlayerLookupTest extends TestCase
{
    use RefreshDatabase;

    private MinecraftPlayerRepository $minecraftPlayerRepository;
    private MinecraftPlayerAliasRepository $minecraftPlayerAliasRepository;
    private PlayerLookup $playerLookup;

    public function setUp(): void
    {
        parent::setUp();

        $this->minecraftPlayerRepository = \Mockery::mock(MinecraftPlayerRepository::class);
        $this->minecraftPlayerAliasRepository = \Mockery::mock(MinecraftPlayerAliasRepository::class);

        $this->playerLookup = new PlayerLookup(
            minecraftPlayerRepository: $this->minecraftPlayerRepository,
            minecraftPlayerAliasRepository: $this->minecraftPlayerAliasRepository,
        );
    }

    public function test_find_returns_null_if_no_player()
    {
        $this->minecraftPlayerRepository
            ->shouldReceive('getByUUID')
            ->andReturnNull();

        $player = $this->playerLookup->find(
            identifier: PlayerIdentifier::minecraftUUID('uuid'),
        );

        $this->assertNull($player);
    }

    public function test_find_returns_minecraft_player_by_uuid()
    {
        $player = MinecraftPlayer::factory()->create();

        $this->minecraftPlayerRepository
            ->shouldReceive('getByUUID')
            ->andReturn($player);

        $actual = $this->playerLookup->find(
            identifier: PlayerIdentifier::minecraftUUID('uuid'),
        );

        $this->assertEquals(
            expected: $player->getKey(),
            actual: $actual->getKey(),
        );
    }

    public function test_find_returns_minecraft_player_by_id()
    {
        $player = MinecraftPlayer::factory()->create();

        $this->minecraftPlayerRepository
            ->shouldReceive('getById')
            ->andReturn($player);

        $actual = $this->playerLookup->find(
            identifier: PlayerIdentifier::pcbAccountId(1),
        );

        $this->assertEquals(
            expected: $player->getKey(),
            actual: $actual->getKey(),
        );
    }

    public function test_findOrCreate_returns_existing_player_by_uuid()
    {
        $player = MinecraftPlayer::factory()->create();

        $this->minecraftPlayerRepository
            ->shouldReceive('getByUUID')
            ->andReturn($player);

        $actual = $this->playerLookup->findOrCreate(
            identifier: PlayerIdentifier::minecraftUUID('uuid'),
        );

        $this->assertEquals(
            expected: $player->getKey(),
            actual: $actual->getKey(),
        );
    }

    public function test_findOrCreate_returns_existing_player_by_id()
    {
        $player = MinecraftPlayer::factory()->create();

        $this->minecraftPlayerRepository
            ->shouldReceive('getById')
            ->andReturn($player);

        $actual = $this->playerLookup->findOrCreate(
            identifier: PlayerIdentifier::pcbAccountId(1),
        );

        $this->assertEquals(
            expected: $player->getKey(),
            actual: $actual->getKey(),
        );
    }

    public function test_findOrCreate_exception_if_looking_up_nonexistent_id()
    {
        $this->minecraftPlayerRepository
            ->shouldReceive('getById')
            ->andReturnNull();

        $this->expectException(NonCreatableIdentifierException::class);

        $player = $this->playerLookup->findOrCreate(
            identifier: PlayerIdentifier::pcbAccountId(34),
        );
    }

    public function test_findOrCreate_creates_new_player()
    {
        $newPlayer = MinecraftPlayer::factory()->make();

        $this->minecraftPlayerRepository
            ->shouldReceive('getByUUID')
            ->andReturnNull();

        $this->minecraftPlayerRepository
            ->shouldReceive('store')
            ->andReturn($newPlayer);

        $player = $this->playerLookup->findOrCreate(
            identifier: PlayerIdentifier::minecraftUUID('uuid'),
        );

        $this->assertEquals(
            expected: $newPlayer->getKey(),
            actual: $player->getKey(),
        );
    }

    public function test_findOrCreate_creates_new_player_with_alias()
    {
        $now = Carbon::create(year: 2022, month: 4, day: 19, hour: 10, minute: 9, second: 8);
        Carbon::setTestNow($now);

        $newPlayer = MinecraftPlayer::factory()->create();
        $alias = 'alias';

        $this->minecraftPlayerRepository
            ->shouldReceive('getByUUID')
            ->andReturnNull();

        $this->minecraftPlayerRepository
            ->shouldReceive('store')
            ->andReturn($newPlayer);

        $this->minecraftPlayerAliasRepository
            ->shouldReceive('store')
            ->andReturn(MinecraftPlayerAlias::factory()->make());

        $player = $this->playerLookup->findOrCreate(
            identifier: PlayerIdentifier::minecraftUUID('uuid'),
            playerAlias: $alias,
        );

        $this->assertEquals(
            expected: $newPlayer->getKey(),
            actual: $player->getKey(),
        );
    }
}
