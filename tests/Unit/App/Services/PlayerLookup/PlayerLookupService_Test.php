<?php

namespace Tests\Unit\App\Services\PlayerLookup;

use App\Services\PlayerLookup\PlayerLookupService;
use Entities\Models\Eloquent\MinecraftPlayer;
use Entities\Models\GamePlayerType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlayerLookupService_Test extends TestCase
{
    use RefreshDatabase;

    public function testCreatesNewPlayer()
    {
        $service = resolve(PlayerLookupService::class);
        $this->assertDatabaseMissing('players_minecraft', [
            'uuid' => 'test_uuid',
        ]);

        $service->getOrCreatePlayer(GamePlayerType::MINECRAFT, 'test_uuid');

        $this->assertDatabaseHas('players_minecraft', [
            'uuid' => 'test_uuid',
        ]);
    }

    public function testGetsExistingPlayer()
    {
        $service = resolve(PlayerLookupService::class);
        MinecraftPlayer::create([
            'uuid' => 'existing_uuid',
        ]);

        $player = $service->getOrCreatePlayer(GamePlayerType::MINECRAFT, 'existing_uuid');

        $this->assertNotNull($player);
        $this->assertEquals('existing_uuid', $player->uuid);
    }
}
