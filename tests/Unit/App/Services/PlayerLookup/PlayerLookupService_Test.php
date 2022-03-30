<?php

namespace Tests\Services;

use App\Entities\Models\Eloquent\MinecraftPlayer;
use App\Entities\Models\GamePlayerType;
use App\Services\PlayerLookup\PlayerLookupService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlayerLookupService_Test extends TestCase
{
    use RefreshDatabase;

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
        ]);

        // when...
        $player = $service->getOrCreatePlayer(GamePlayerType::Minecraft(), 'existing_uuid');

        // expect...
        $this->assertNotNull($player);
        $this->assertEquals('existing_uuid', $player->uuid);
    }
}
