<?php

namespace Tests\Unit\Domain\Bans\UseCases;

use App\Core\Domains\Mojang\Api\MojangPlayerApi;
use App\Core\Domains\Mojang\Models\MojangPlayer;
use App\Domains\Bans\Exceptions\NotBannedException;
use App\Domains\Bans\UseCases\LookupPlayerBan;
use App\Models\GamePlayerBan;
use App\Models\MinecraftPlayer;
use Repositories\GamePlayerBanRepository;
use Repositories\MinecraftPlayerRepository;
use Shared\PlayerLookup\Exceptions\PlayerNotFoundException;
use Tests\TestCase;

class LookupBanTest extends TestCase
{
    private MojangPlayerApi $mojangPlayerApi;
    private GamePlayerBanRepository $gamePlayerBanRepository;
    private MinecraftPlayerRepository $minecraftPlayerRepository;
    private LookupPlayerBan $useCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mojangPlayerApi = \Mockery::mock(MojangPlayerApi::class);
        $this->gamePlayerBanRepository = \Mockery::mock(GamePlayerBanRepository::class);
        $this->minecraftPlayerRepository = \Mockery::mock(MinecraftPlayerRepository::class);

        $this->useCase = new LookupPlayerBan(
            mojangPlayerApi: $this->mojangPlayerApi,
            gamePlayerBanRepository: $this->gamePlayerBanRepository,
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

    public function mockgamePlayerBanRepositoryToReturn($playerId, $bans)
    {
        $this->gamePlayerBanRepository
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
        $this->mockgamePlayerBanRepositoryToReturn($mcPlayer->getKey(), null);

        $this->expectException(NotBannedException::class);

        $this->useCase->execute('Herobrine');
    }

    public function test_returns_active_ban_if_exists()
    {
        $mcPlayer = MinecraftPlayer::factory()->create(['uuid' => 'abc123']);
        $this->mockMojangApiToReturn('Herobrine', 'abc123');
        $this->mockPlayerRepositoryToReturn('abc123', $mcPlayer);
        $gamePlayerBanInst = GamePlayerBan::factory()->make();
        $this->mockgamePlayerBanRepositoryToReturn($mcPlayer->getKey(), $gamePlayerBanInst);

        $this->assertEquals(
            $gamePlayerBanInst,
            $this->useCase->execute('Herobrine')
        );
    }
}
