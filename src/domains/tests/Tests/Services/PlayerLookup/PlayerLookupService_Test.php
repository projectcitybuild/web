<?php
namespace Tests\Services;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Domains\Services\PlayerLookup\PlayerLookupService;
use Domains\Modules\GamePlayerType;
use Domains\Modules\Players\Models\MinecraftPlayer;

class PlayerLookupService_Test extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;

    public function testCreatesNewPlayer()
    {
        // given...
        $service = resolve(PlayerLookupService::class);
        $this->assertDatabaseMissing('players_minecraft', [
            'uuid' => 'test_uuid', 
        ]);

        // when...
        $service->getOrCreatePlayer(GamePlayerType::Minecraft(), 'test_uuid');

        // expect...
        $this->assertDatabaseHas('players_minecraft', [
            'uuid' => 'test_uuid', 
        ]);
    }

    public function testGetsExistingPlayer()
    {
        // given...
        $service = resolve(PlayerLookupService::class);
        MinecraftPlayer::create([
            'uuid' => 'existing_uuid',
            'playtime' => 0,
            'last_seen_at' => now(),
        ]);

        // when...
        $player = $service->getOrCreatePlayer(GamePlayerType::Minecraft(), 'existing_uuid');

        // expect...
        $this->assertNotNull($player);
        $this->assertEquals('existing_uuid', $player->uuid);
    }
}