<?php

namespace Tests\Unit\Domain\Warnings\UseCases;

use Domain\Warnings\UseCases\GetWarnings;
use Entities\Models\Eloquent\MinecraftPlayer;
use Entities\Models\Eloquent\PlayerWarning;
use Repositories\Warnings\MockPlayerWarningRepository;
use Repositories\Warnings\PlayerWarningRepository;
use Shared\PlayerLookup\Entities\PlayerIdentifier;
use Shared\PlayerLookup\Service\MockPlayerLookup;
use Shared\PlayerLookup\Service\PlayerLookup;
use Tests\TestCase;

class GetWarningsTest extends TestCase
{
    private PlayerWarningRepository $playerWarningRepository;
    private PlayerLookup $playerLookup;
    private GetWarnings $useCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->playerWarningRepository = new MockPlayerWarningRepository();
        $this->playerLookup = new MockPlayerLookup();

        $this->useCase = new GetWarnings(
            playerLookup: $this->playerLookup,
            playerWarningRepository: $this->playerWarningRepository,
        );
    }

    public function test_returns_all_warnings()
    {
        $expectedWarnings = collect([
            PlayerWarning::factory()->withPlayers()->make(),
            PlayerWarning::factory()->withPlayers()->make(),
        ]);
        $this->playerWarningRepository->all = $expectedWarnings;
        $this->playerLookup->find = MinecraftPlayer::factory()->id()->make();

        $warnings = $this->useCase->execute(
            playerIdentifier: PlayerIdentifier::minecraftUUID('test'),
            playerAlias: 'alias',
        );

        $this->assertEquals(expected: $expectedWarnings, actual: $warnings);
    }

    public function test_returns_empty_collection_if_no_warnings()
    {
        $this->playerWarningRepository->all = collect();
        $this->playerLookup->find = MinecraftPlayer::factory()->id()->make();

        $warnings = $this->useCase->execute(
            playerIdentifier: PlayerIdentifier::minecraftUUID('test'),
            playerAlias: 'alias',
        );

        $this->assertEquals(expected: collect(), actual: $warnings);
    }

    public function test_returns_empty_collection_if_player_not_found()
    {
        $this->playerLookup->find = null;

        $warnings = $this->useCase->execute(
            playerIdentifier: PlayerIdentifier::minecraftUUID('test'),
            playerAlias: 'alias',
        );

        $this->assertEquals(expected: collect(), actual: $warnings);
    }
}
