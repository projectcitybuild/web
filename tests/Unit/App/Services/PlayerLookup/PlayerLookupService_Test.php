<?php

namespace Tests\Unit\App\Services\PlayerLookup;

use App\Services\PlayerLookup\PlayerLookupService;
use Entities\Models\Eloquent\MinecraftPlayer;
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

        $service->getOrCreatePlayer('test_uuid');

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

        $player = $service->getOrCreatePlayer('existing_uuid');

        $this->assertNotNull($player);
        $this->assertEquals('existing_uuid', $player->uuid);
    }
}
