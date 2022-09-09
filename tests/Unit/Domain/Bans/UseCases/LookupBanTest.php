<?php

namespace Tests\Unit\Domain\Bans\UseCases;

use Domain\Bans\Exceptions\NotBannedException;
use Domain\Bans\UseCases\LookupBan;
use Entities\Models\Eloquent\GameBan;
use Entities\Models\Eloquent\MinecraftPlayer;
use Library\Mojang\Api\MojangPlayerApi;
use Library\Mojang\Models\MojangPlayer;
use Repositories\GameBanRepository;
use Repositories\MinecraftPlayerRepository;
use Shared\PlayerLookup\Exceptions\PlayerNotFoundException;
use Tests\TestCase;

class LookupBanTest extends TestCase
{
    private MojangPlayerApi $mojangPlayerApi;
    private GameBanRepository $gameBanRepository;
    private MinecraftPlayerRepository $minecraftPlayerRepository;
    private LookupBan $useCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mojangPlayerApi = \Mockery::mock(MojangPlayerApi::class);
        $this->gameBanRepository = \Mockery::mock(GameBanRepository::class);
        $this->minecraftPlayerRepository = \Mockery::mock(MinecraftPlayerRepository::class);

        $this->useCase = new LookupBan(
            mojangPlayerApi: $this->mojangPlayerApi,
            gameBanRepository: $this->gameBanRepository,
            minecraftPlayerRepository: $this->minecraftPlayerRepository
        );
    }

    private function mockMojangApiToReturn($username, $uuid)
    {
        $this->mojangPlayerApi
            ->shouldReceive('getUuidOf')
            ->once()
            ->with($username)
            ->once()->andReturn(
                new MojangPlayer($uuid, $username)
            );
    }

    private function mockPlayerRepositoryToReturn($uuid, $player)
    {
        $this->minecraftPlayerRepository
            ->shouldReceive('getByUUID')
            ->once()
            ->with(\Mockery::on(function ($arg) use ($uuid) {
                return $arg->rawValue() == $uuid;
            }))
            ->andReturn($player);
    }

    public function mockGameBanRepositoryToReturn($playerId, $bans)
    {
        $this->gameBanRepository
            ->shouldReceive('firstActiveBan')
            ->once()
            ->with(
                \Mockery::on(function ($arg) use ($playerId) {
                    return $arg->getKey() == $playerId;
                }),
            )
            ->andReturn($bans);
    }

    public function test_throws_exception_if_player_doesnt_exist()
    {
        $this->mojangPlayerApi
            ->shouldReceive('getUuidOf')
            ->once()
            ->with('Herobrine')
            ->andReturn(null);
        $this->expectException(PlayerNotFoundException::class);

        $this->useCase->execute('Herobrine');
    }

    public function test_throws_exception_if_player_not_known()
    {
        $this->mockMojangApiToReturn('Herobrine', 'abc123');
        $this->mockPlayerRepositoryToReturn('abc123', null);
        $this->expectException(NotBannedException::class);
        $this->useCase->execute('Herobrine');
    }

    public function test_throws_exception_if_no_active_bans()
    {
        $mcPlayer = MinecraftPlayer::factory()->create(['uuid' => 'abc123']);
        $this->mockMojangApiToReturn('Herobrine', 'abc123');
        $this->mockPlayerRepositoryToReturn('abc123', $mcPlayer);
        $this->mockGameBanRepositoryToReturn($mcPlayer->getKey(), null);

        $this->expectException(NotBannedException::class);

        $this->useCase->execute('Herobrine');
    }

    public function test_returns_active_ban_if_exists()
    {
        $mcPlayer = MinecraftPlayer::factory()->create(['uuid' => 'abc123']);
        $this->mockMojangApiToReturn('Herobrine', 'abc123');
        $this->mockPlayerRepositoryToReturn('abc123', $mcPlayer);
        $gameBanInst = GameBan::factory()->make();
        $this->mockGameBanRepositoryToReturn($mcPlayer->getKey(), $gameBanInst);

        $this->assertEquals(
            $gameBanInst,
            $this->useCase->execute('Herobrine')
        );
    }
}