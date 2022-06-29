<?php

namespace Unit\Domain\Bans\UseCases;

use Domain\Bans\Exceptions\PlayerNotBannedException;
use Domain\Bans\UseCases\LookupBanUseCase;
use Entities\Models\Eloquent\GameBan;
use Entities\Models\Eloquent\MinecraftPlayer;
use Library\Mojang\Api\MojangPlayerApi;
use Library\Mojang\Models\MojangPlayer;
use Repositories\GameBanRepository;
use Shared\PlayerLookup\Exceptions\PlayerNotFoundException;
use Shared\PlayerLookup\Repositories\MinecraftPlayerRepository;
use Tests\TestCase;

class LookupBanUseCaseTest extends TestCase
{
    private MojangPlayerApi $mojangPlayerApi;
    private GameBanRepository $gameBanRepository;
    private MinecraftPlayerRepository $minecraftPlayerRepository;
    private LookupBanUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mojangPlayerApi = \Mockery::mock(MojangPlayerApi::class);
        $this->gameBanRepository = \Mockery::mock(GameBanRepository::class);
        $this->minecraftPlayerRepository = \Mockery::mock(MinecraftPlayerRepository::class);

        $this->useCase = new LookupBanUseCase(
            mojangPlayerApi: $this->mojangPlayerApi,
            gameBanRepository: $this->gameBanRepository,
            minecraftPlayerRepository: $this->minecraftPlayerRepository
        );
    }

    private function mockMojangApiToReturn($username, $uuid)
    {
        $this->mojangPlayerApi->shouldReceive('getUuidOf')
            ->once()
            ->with($username)
            ->once()->andReturn(
                new MojangPlayer($uuid, $username)
            );
    }

    private function mockPlayerRepositoryToReturn($uuid, $player)
    {
        $this->minecraftPlayerRepository->shouldReceive('getByUUID')
            ->once()
            ->with(\Mockery::on(function ($arg) use ($uuid) {
                return $arg->rawValue() == $uuid;
            }))
            ->andReturn($player);
    }

    public function mockGameBanRepositoryToReturn($id, $bans)
    {
        $this->gameBanRepository->shouldReceive('firstActiveBan')
            ->once()
            ->with(\Mockery::on(function ($arg) use ($id) {
                return $arg->key == $id;
            }))
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
        $this->expectException(PlayerNotBannedException::class);
        $this->useCase->execute('Herobrine');
    }

    public function test_throws_exception_if_no_active_bans()
    {
        $this->mockMojangApiToReturn('Herobrine', 'abc123');
        $this->mockPlayerRepositoryToReturn('abc123', MinecraftPlayer::factory()->create(['uuid' => 'abc123']));
        $this->mockGameBanRepositoryToReturn(1, null);

        $this->expectException(PlayerNotBannedException::class);

        $this->useCase->execute('Herobrine');
    }

    public function test_returns_active_ban_if_exists()
    {
        $this->mockMojangApiToReturn('Herobrine', 'abc123');
        $this->mockPlayerRepositoryToReturn('abc123', MinecraftPlayer::factory()->create(['uuid' => 'abc123']));
        $gameBanInst = GameBan::factory()->make();
        $this->mockGameBanRepositoryToReturn(1, $gameBanInst);

        $this->assertEquals(
            $gameBanInst,
            $this->useCase->execute('Herobrine')
        );
    }
}
