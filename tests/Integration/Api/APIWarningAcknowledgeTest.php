<?php

namespace Tests\Integration\API;

use App\Models\MinecraftPlayer;
use App\Models\PlayerWarning;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\IntegrationTestCase;

class APIWarningAcknowledgeTest extends IntegrationTestCase
{
    use RefreshDatabase;

    private const ENDPOINT = 'api/v2/warnings/acknowledge';

    protected function setUp(): void
    {
        parent::setUp();

        $this->createServerToken();
    }

    private function validData(): array
    {
        $warning = PlayerWarning::factory()
            ->for(MinecraftPlayer::factory(), 'warnedPlayer')
            ->for(MinecraftPlayer::factory(), 'warnerPlayer')
            ->acknowledged(false)
            ->create();

        return ['warning_id' => $warning->getKey()];
    }

    public function test_requires_scope()
    {
        $this->postJson(uri: self::ENDPOINT, data: $this->validData())
            ->assertUnauthorized();

        $this->withAuthorizationServerToken()
            ->postJson(uri: self::ENDPOINT, data: $this->validData())
            ->assertSuccessful();
    }

    public function test_acknowledges_warning()
    {
        $player1 = MinecraftPlayer::factory()->create(['uuid' => 'uuid1']);
        $player2 = MinecraftPlayer::factory()->create(['uuid' => 'uuid2']);

        $warning = PlayerWarning::factory()
            ->for($player1, 'warnedPlayer')
            ->for($player2, 'warnerPlayer')
            ->acknowledged(false)
            ->create();

        $this->assertDatabaseHas(
            table: PlayerWarning::tableName(),
            data: [
                'id' => $warning->getKey(),
                'warned_player_id' => $player1->getKey(),
                'warner_player_id' => $player2->getKey(),
                'reason' => $warning->reason,
                'additional_info' => $warning->additional_info,
                'weight' => $warning->weight,
                'is_acknowledged' => false,
                'created_at' => $warning->created_at,
                'updated_at' => $warning->updated_at,
                'acknowledged_at' => null,
            ],
        );

        $this->withAuthorizationServerToken()
            ->postJson(uri: self::ENDPOINT, data: [
                'warning_id' => $warning->getKey(),
            ])
            ->assertSuccessful();

        $this->assertDatabaseHas(
            table: PlayerWarning::tableName(),
            data: [
                'id' => $warning->getKey(),
                'warned_player_id' => $player1->getKey(),
                'warner_player_id' => $player2->getKey(),
                'reason' => $warning->reason,
                'additional_info' => $warning->additional_info,
                'weight' => $warning->weight,
                'is_acknowledged' => true,
                'created_at' => $warning->created_at,
                'updated_at' => now(),
                'acknowledged_at' => now(),
            ],
        );
    }
}
