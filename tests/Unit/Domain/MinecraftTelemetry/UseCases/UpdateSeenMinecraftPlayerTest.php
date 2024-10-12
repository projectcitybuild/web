<?php

namespace Tests\Unit\Domain\MinecraftTelemetry\UseCases;

use App\Core\Domains\PlayerLookup\Service\ConcretePlayerLookup;
use App\Domains\MinecraftTelemetry\UseCases\UpdateSeenMinecraftPlayer;
use App\Models\MinecraftPlayer;
use App\Models\MinecraftPlayerAlias;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateSeenMinecraftPlayerTest extends TestCase
{
    use RefreshDatabase;

    private UpdateSeenMinecraftPlayer $useCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->useCase = new UpdateSeenMinecraftPlayer();
    }

    public function test_updates_last_seen_date()
    {
        $now = $this->setTestNow();
        $before = $now->copy()->subWeek();

        $player = MinecraftPlayer::factory()->create([
            'uuid' => 'uuid',
            'alias' => 'alias',
            'last_seen_at' => $before,
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

    public function test_updates_alias()
    {
        $now = $this->setTestNow();

        $player = MinecraftPlayer::factory()->create([
            'uuid' => 'uuid',
            'alias' => 'old_alias',
            'last_synced_at' => $now->copy()->subWeek(),
        ]);

        $this->assertDatabaseHas('players_minecraft', [
            'uuid' => 'uuid',
            'alias' => 'old_alias',
        ]);

        $this->playerLookup
            ->shouldReceive('findOrCreate')
            ->andReturn($player);

        $this->useCase->execute(uuid: 'uuid', alias: 'new_alias');

        $this->assertDatabaseHas('players_minecraft', [
           'uuid' => 'uuid',
           'alias' => 'new_alias',
        ]);
    }
}
