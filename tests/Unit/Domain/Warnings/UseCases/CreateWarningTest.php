<?php

namespace Tests\Unit\Domain\Warnings\UseCases;

use Domain\Warnings\UseCases\CreateWarning;
use Entities\Models\Eloquent\MinecraftPlayer;
use Entities\Models\Eloquent\PlayerWarning;
use Repositories\Warnings\MockPlayerWarningRepository;
use Repositories\Warnings\PlayerWarningRepository;
use Shared\PlayerLookup\Entities\PlayerIdentifier;
use Shared\PlayerLookup\Service\MockPlayerLookup;
use Shared\PlayerLookup\Service\PlayerLookup;
use Tests\TestCase;

class CreateWarningTest extends TestCase
{
    private PlayerWarningRepository $playerWarningRepository;
    private PlayerLookup $playerLookup;
    private CreateWarning $useCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->playerWarningRepository = new MockPlayerWarningRepository();
        $this->playerLookup = new MockPlayerLookup();

        $this->useCase = new CreateWarning(
            playerLookup: $this->playerLookup,
            playerWarningRepository: $this->playerWarningRepository,
        );
    }

    public function test_creates_warning()
    {
        $createdWarning = PlayerWarning::factory()->withPlayers()->make();
        $this->playerWarningRepository->create = $createdWarning;
        $this->playerLookup->findOrCreate = MinecraftPlayer::factory()->make();

        $warning = $this->useCase->execute(
            warnedPlayerIdentifier: PlayerIdentifier::minecraftUUID('uuid1'),
            warnedPlayerAlias: 'alias1',
            warnerPlayerIdentifier: PlayerIdentifier::minecraftUUID('uuid2'),
            warnerPlayerAlias: 'alias2',
            reason: 'reason',
            weight: 5,
            isAcknowledged: false,
        );

        $this->assertEquals(expected: $createdWarning, actual: $warning);
    }
}
