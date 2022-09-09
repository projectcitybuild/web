<?php

namespace Tests\Unit\Domain\Bans\UseCases;

use Domain\Bans\Exceptions\AlreadyPermBannedException;
use Domain\Bans\UseCases\CreateBan;
use Entities\Models\Eloquent\GameBan;
use Entities\Models\Eloquent\MinecraftPlayer;
use Repositories\GameBanRepository;
use Shared\PlayerLookup\Entities\PlayerIdentifier;
use Shared\PlayerLookup\PlayerLookup;
use Tests\TestCase;

class CreateBanUseCaseTest extends TestCase
{
    private readonly PlayerLookup $playerLookup;
    private readonly GameBanRepository $gameBanRepository;
    private readonly CreateBan $useCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->gameBanRepository = \Mockery::mock(GameBanRepository::class);
        $this->playerLookup = \Mockery::mock(PlayerLookup::class);

        $this->useCase = new CreateBan(
            gameBanRepository: $this->gameBanRepository,
            playerLookup: $this->playerLookup,
        );
    }

    public function test_throws_exception_if_already_banned()
    {
        $this->playerLookup
            ->shouldReceive('findOrCreate')
            ->andReturn(MinecraftPlayer::factory()->make());

        $this->gameBanRepository
            ->shouldReceive('firstActiveBan')
            ->andReturn(GameBan::factory()->make());

        $this->expectException(AlreadyPermBannedException::class);

        $this->useCase->execute(
            serverId: 1,
            bannedPlayerIdentifier: PlayerIdentifier::minecraftUUID('uuid1'),
            bannedPlayerAlias: 'Herobrine',
            bannerPlayerIdentifier: PlayerIdentifier::minecraftUUID('uuid2'),
            bannerPlayerAlias: 'Notch',
            banReason: 'test',
            expiresAt: null,
        );
    }

    public function test_creates_ban()
    {
        $bannedPlayer = MinecraftPlayer::factory()->create();
        $bannerPlayer = MinecraftPlayer::factory()->create();
        $ban = GameBan::factory()->make();

        $this->playerLookup
            ->shouldReceive('findOrCreate')
            ->andReturn($bannedPlayer);

        $this->playerLookup
            ->shouldReceive('findOrCreate')
            ->andReturn($bannerPlayer);

        $this->gameBanRepository
            ->shouldReceive('firstActiveBan')
            ->andReturn(null);

        $this->gameBanRepository
            ->shouldReceive('create')
            ->andReturn($ban);

        $this->gameBanRepository
            ->shouldReceive('deactivateAllTemporaryBans');

        $returnedBan = $this->useCase->execute(
            serverId: 1,
            bannedPlayerIdentifier: PlayerIdentifier::minecraftUUID('uuid1'),
            bannedPlayerAlias: 'Herobrine',
            bannerPlayerIdentifier: PlayerIdentifier::minecraftUUID('uuid2'),
            bannerPlayerAlias: 'Notch',
            banReason: 'test',
            expiresAt: null,
        );

        $this->assertEquals(expected: $ban, actual: $returnedBan);
    }
}
