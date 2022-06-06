<?php

namespace Tests\Unit\Domain\MinecraftTelemetry\UseCases;

use Domain\MinecraftTelemetry\UseCases\UpdateSeenMinecraftPlayerUseCase;
use Entities\Models\Eloquent\MinecraftPlayer;
use Entities\Models\Eloquent\MinecraftPlayerAlias;
use Entities\Repositories\MinecraftPlayerAliasRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Shared\PlayerLookup\PlayerLookup;
use Tests\TestCase;

class UpdateSeenMinecraftPlayerUseCaseTest extends TestCase
{
    use RefreshDatabase;

    private PlayerLookup $playerLookup;
    private MinecraftPlayerAliasRepository $aliasRepository;
    private UpdateSeenMinecraftPlayerUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->playerLookup = \Mockery::mock(PlayerLookup::class);
        $this->aliasRepository = \Mockery::mock(MinecraftPlayerAliasRepository::class);

        $this->useCase = new UpdateSeenMinecraftPlayerUseCase(
            playerLookup: $this->playerLookup,
            aliasRepository: $this->aliasRepository,
        );
    }

    public function test_updates_last_seen_date()
    {
        $now = $this->setTestNow();
        $before = $now->copy()->subWeek();

        $player = MinecraftPlayer::factory()->create([
            'uuid' => 'uuid',
            'last_synced_at' => $before,
        ]);
        MinecraftPlayerAlias::factory()->for($player)->create([
            'alias' => 'alias',
        ]);

        $this->playerLookup
            ->shouldReceive('findOrCreate')
            ->andReturn($player);

        $this->assertEquals(
            expected: $before,
            actual: $player->last_synced_at,
        );

        $this->useCase->execute(uuid: 'uuid', alias: 'alias');

        $this->assertEquals(
            expected: $now,
            actual: $player->last_synced_at,
        );
    }

    public function test_creates_alias_if_player_changed_alias()
    {
        $now = $this->setTestNow();

        $player = MinecraftPlayer::factory()->create([
            'uuid' => 'uuid',
            'last_synced_at' => $now->copy()->subWeek(),
        ]);
        MinecraftPlayerAlias::factory()->for($player)->create([
            'alias' => 'old_alias',
        ]);

        $this->playerLookup
            ->shouldReceive('findOrCreate')
            ->andReturn($player);

        $this->aliasRepository
            ->shouldReceive('store')
            ->andReturn(new MinecraftPlayerAlias());

        $this->useCase->execute(uuid: 'uuid', alias: 'new_alias');
    }
}
