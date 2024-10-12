<?php

namespace Tests\Unit\Domain\Warnings\UseCases;

use App\Core\Domains\PlayerLookup\Data\PlayerIdentifier;
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
    private PlayerLookup $playerLookup;
    private CreateWarning $useCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->playerLookup = new PlayerLookupMock();

        $this->useCase = new CreateWarning(
            playerLookup: $this->playerLookup,
        );
    }

    public function test_creates_warning()
    {
        $createdWarning = PlayerWarning::factory()->id()->withPlayers()->make();
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

        // TODO
        $this->fail();

        $this->assertEquals(expected: $createdWarning, actual: $warning);
    }
}
