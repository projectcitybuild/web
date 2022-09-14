<?php

namespace Tests\Unit\Domain\Warnings\UseCases;

use Domain\Warnings\UseCases\GetWarnings;
use Entities\Models\Eloquent\MinecraftPlayer;
use Entities\Models\Eloquent\PlayerWarning;
use Repositories\Warnings\MockPlayerWarningRepository;
use Repositories\Warnings\PlayerWarningRepository;
use Shared\PlayerLookup\Entities\PlayerIdentifier;
use Shared\PlayerLookup\PlayerLookup;
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
        $this->playerLookup = \Mockery::mock(PlayerLookup::class);

        $this->useCase = new GetWarnings(
            playerLookup: $this->playerLookup,
            playerWarningRepository: $this->playerWarningRepository,
        );
    }

    private function makeWarning(): PlayerWarning
    {
        return PlayerWarning::factory()
            ->warnedPlayer(MinecraftPlayer::factory())
            ->warnedBy(MinecraftPlayer::factory())
            ->make();
    }

    public function test_returns_all_warnings()
    {
        $this->playerLookup
            ->shouldReceive('find')
            ->andReturn(MinecraftPlayer::factory()->make());

        $expectedWarnings = collect([
            $this->makeWarning(),
            $this->makeWarning(),
        ]);
        $this->playerWarningRepository->all = $expectedWarnings;

        $warnings = $this->useCase->execute(
            playerIdentifier: PlayerIdentifier::minecraftUUID('test'),
            playerAlias: 'alias',
        );

        $this->assertEquals(expected: $expectedWarnings, actual: $warnings);
    }

    public function test_returns_empty_collection_if_no_warnings()
    {
        $this->playerLookup
            ->shouldReceive('find')
            ->andReturn(MinecraftPlayer::factory()->make());

        $this->playerWarningRepository->all = collect();

        $warnings = $this->useCase->execute(
            playerIdentifier: PlayerIdentifier::minecraftUUID('test'),
            playerAlias: 'alias',
        );

        $this->assertEquals(expected: collect(), actual: $warnings);
    }

    public function test_returns_empty_collection_if_player_not_found()
    {
        $this->playerLookup
            ->shouldReceive('find')
            ->andReturnNull();

        $warnings = $this->useCase->execute(
            playerIdentifier: PlayerIdentifier::minecraftUUID('test'),
            playerAlias: 'alias',
        );

        $this->assertEquals(expected: collect(), actual: $warnings);
    }
}
