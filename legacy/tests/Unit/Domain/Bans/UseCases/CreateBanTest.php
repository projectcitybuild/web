<?php

namespace Tests\Unit\Domain\Bans\UseCases;

use Domain\Bans\Exceptions\AlreadyPermBannedException;
use Domain\Bans\UseCases\CreatePlayerBan;
use Entities\Models\Eloquent\GamePlayerBan;
use Entities\Models\Eloquent\MinecraftPlayer;
use Repositories\GamePlayerBanRepository;
use Shared\PlayerLookup\Entities\PlayerIdentifier;
use Shared\PlayerLookup\Service\ConcretePlayerLookup;
use Tests\TestCase;

class CreateBanTest extends TestCase
{
    private readonly ConcretePlayerLookup $playerLookup;
    private readonly GamePlayerBanRepository $gamePlayerBanRepository;
    private readonly CreatePlayerBan $useCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->gamePlayerBanRepository = \Mockery::mock(GamePlayerBanRepository::class);
        $this->playerLookup = \Mockery::mock(ConcretePlayerLookup::class);

        $this->useCase = new CreatePlayerBan(
            gamePlayerBanRepository: $this->gamePlayerBanRepository,
            playerLookup: $this->playerLookup,
        );
    }

    public function test_throws_exception_if_already_banned()
    {
        $this->playerLookup
            ->shouldReceive('findOrCreate')
            ->andReturn(MinecraftPlayer::factory()->make());

        $this->gamePlayerBanRepository
            ->shouldReceive('firstActiveBan')
            ->andReturn(GamePlayerBan::factory()->make());

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
        $ban = GamePlayerBan::factory()->make();

        $this->playerLookup
            ->shouldReceive('findOrCreate')
            ->andReturn($bannedPlayer);

        $this->playerLookup
            ->shouldReceive('findOrCreate')
            ->andReturn($bannerPlayer);

        $this->gamePlayerBanRepository
            ->shouldReceive('firstActiveBan')
            ->andReturn(null);

        $this->gamePlayerBanRepository
            ->shouldReceive('create')
            ->andReturn($ban);

        $this->gamePlayerBanRepository
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