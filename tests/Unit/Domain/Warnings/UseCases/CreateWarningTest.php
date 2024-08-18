<?php

namespace Tests\Unit\Domain\Warnings\UseCases;

use App\Core\Domains\PlayerLookup\Entities\PlayerIdentifier;
use App\Core\Domains\PlayerLookup\Service\PlayerLookup;
use App\Core\Domains\PlayerLookup\Service\PlayerLookupMock;
use App\Domains\Warnings\UseCases\CreateWarning;
use App\Models\MinecraftPlayer;
use App\Models\PlayerWarning;
use Repositories\PlayerWarnings\PlayerWarningMockRepository;
use Repositories\PlayerWarnings\PlayerWarningRepository;
use Tests\TestCase;

class CreateWarningTest extends TestCase
{
    private PlayerWarningRepository $playerWarningRepository;
    private PlayerLookup $playerLookup;
    private CreateWarning $useCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->playerWarningRepository = new PlayerWarningMockRepository();
        $this->playerLookup = new PlayerLookupMock();

        $this->useCase = new CreateWarning(
            playerLookup: $this->playerLookup,
            playerWarningRepository: $this->playerWarningRepository,
        );
    }

    public function test_creates_warning()
    {
        $createdWarning = PlayerWarning::factory()->id()->withPlayers()->make();
        $this->playerWarningRepository->create = $createdWarning;
        $this->playerLookup->findOrCreate = MinecraftPlayer::factory()->id()->make();

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
