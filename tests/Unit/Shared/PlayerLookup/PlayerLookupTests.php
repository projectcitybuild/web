<?php

namespace Tests\Unit\Shared\PlayerLookup;

use Entities\MinecraftUUID;
use Entities\Models\Eloquent\MinecraftPlayer;
use Entities\Repositories\MinecraftPlayerAliasRepository;
use Entities\Repositories\MinecraftPlayerRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Shared\PlayerLookup\Entities\PlayerIdentifier;
use Shared\PlayerLookup\PlayerLookup;
use Tests\TestCase;

class PlayerLookupTests extends TestCase
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
        $uuid = new MinecraftUUID('uuid');

        $this->minecraftPlayerRepository
            ->shouldReceive('getByUUID')
            ->with($uuid)
            ->andReturnNull();

        $player = $this->playerLookup->find(
            identifier: PlayerIdentifier::minecraftUUID($uuid),
        );

        $this->assertNull($player);
    }

    public function test_find_returns_minecraft_player()
    {
        $uuid = new MinecraftUUID('uuid');
        $player = MinecraftPlayer::factory()->create();

        $this->minecraftPlayerRepository
            ->shouldReceive('getByUUID')
            ->with($uuid)
            ->andReturn($player);

        $actual = $this->playerLookup->find(
            identifier: PlayerIdentifier::minecraftUUID($uuid),
        );

        $this->assertEquals(
            expected: $player->getKey(),
            actual: $actual->getKey(),
        );
    }

    public function test_findOrCreate_returns_existing_player()
    {
        $uuid = new MinecraftUUID('uuid');
        $player = MinecraftPlayer::factory()->create();

        $this->minecraftPlayerRepository
            ->shouldReceive('getByUUID')
            ->with($uuid)
            ->andReturn($player);

        $actual = $this->playerLookup->findOrCreate(
            identifier: PlayerIdentifier::minecraftUUID($uuid),
        );

        $this->assertEquals(
            expected: $player->getKey(),
            actual: $actual->getKey(),
        );
    }

    public function test_findOrCreate_creates_new_player()
    {
        $uuid = new MinecraftUUID('uuid');
        $newPlayer = MinecraftPlayer::factory()->make();

        $this->minecraftPlayerRepository
            ->shouldReceive('getByUUID')
            ->with($uuid)
            ->andReturnNull();

        $this->minecraftPlayerRepository
            ->shouldReceive('store')
            ->andReturn($newPlayer);

        $player = $this->playerLookup->findOrCreate(
            identifier: PlayerIdentifier::minecraftUUID($uuid),
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

        $uuid = new MinecraftUUID('uuid');
        $newPlayer = MinecraftPlayer::factory()->make();
        $alias = 'alias';

        $this->minecraftPlayerRepository
            ->shouldReceive('getByUUID')
            ->with($uuid)
            ->andReturnNull();

        $this->minecraftPlayerRepository
            ->shouldReceive('store')
            ->andReturn($newPlayer);

        $this->minecraftPlayerAliasRepository
            ->shouldReceive('store')
            ->with($newPlayer->getKey(), $alias, $now);

        $player = $this->playerLookup->findOrCreate(
            identifier: PlayerIdentifier::minecraftUUID($uuid),
            playerAlias: $alias,
        );

        $this->assertEquals(
            expected: $newPlayer->getKey(),
            actual: $player->getKey(),
        );
    }
}
