<?php

namespace Tests\Unit\Domain\MinecraftTelemetry\UseCases;

use App\Core\Domains\PlayerLookup\Service\ConcretePlayerLookup;
use App\Domains\MinecraftTelemetry\UseCases\UpdateSeenMinecraftPlayer;
use App\Models\MinecraftPlayer;
use App\Models\MinecraftPlayerAlias;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Repositories\MinecraftPlayerAliasRepository;
use Tests\TestCase;

class UpdateSeenMinecraftPlayerTest extends TestCase
{
    use RefreshDatabase;

    private ConcretePlayerLookup $playerLookup;
    private MinecraftPlayerAliasRepository $aliasRepository;
    private UpdateSeenMinecraftPlayer $useCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->playerLookup = \Mockery::mock(ConcretePlayerLookup::class);
        $this->aliasRepository = \Mockery::mock(MinecraftPlayerAliasRepository::class);

        $this->useCase = new UpdateSeenMinecraftPlayer(
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
            'last_seen_at' => $before,
        ]);
        MinecraftPlayerAlias::factory()->for($player)->create([
            'alias' => 'alias',
        ]);

        $this->playerLookup
            ->shouldReceive('findOrCreate')
            ->andReturn($player);

        $this->assertEquals(
            expected: $before,
            actual: $player->last_seen_at,
        );

        $this->useCase->execute(uuid: 'uuid', alias: 'alias');

        $this->assertEquals(
            expected: $now,
            actual: $player->last_seen_at,
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
