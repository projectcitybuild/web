<?php

namespace Tests\Integration\API;

use App\Domains\ServerTokens\ScopeKey;
use App\Models\MinecraftPlayer;
use App\Models\PlayerWarning;
use Entities\Models\PlayerIdentifierType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\IntegrationTestCase;

class APIWarningCreateTest extends IntegrationTestCase
{
    use RefreshDatabase;

    private const ENDPOINT = 'api/v2/warnings/';

    protected function setUp(): void
    {
        parent::setUp();

        $this->createServerToken();
    }

    private function validData(): array
    {
        return [
            'warned_player_type' => PlayerIdentifierType::MINECRAFT_UUID->value,
            'warned_player_id' => 'uuid1',
            'warned_player_alias' => 'alias1',
            'warner_player_type' => PlayerIdentifierType::MINECRAFT_UUID->value,
            'warner_player_id' => 'uuid2',
            'warner_player_alias' => 'alias2',
            'reason' => 'reason',
            'weight' => 5,
        ];
    }

    public function test_requires_scope()
    {
        $this->withAuthorizationServerToken()
            ->postJson(uri: self::ENDPOINT, data: $this->validData())
            ->assertForbidden();

        $this->authoriseTokenFor(ScopeKey::WARNING_UPDATE);

        $this->withAuthorizationServerToken()
            ->postJson(uri: self::ENDPOINT, data: $this->validData())
            ->assertSuccessful();
    }

    public function test_creates_warning()
    {
        $this->authoriseTokenFor(ScopeKey::WARNING_UPDATE);

        $player1 = MinecraftPlayer::factory()->create(['uuid' => 'uuid1']);
        $player2 = MinecraftPlayer::factory()->create(['uuid' => 'uuid2']);

        $this->withAuthorizationServerToken()
            ->postJson(uri: self::ENDPOINT, data: [
                'warned_player_type' => PlayerIdentifierType::MINECRAFT_UUID->value,
                'warned_player_id' => $player1->uuid,
                'warned_player_alias' => 'alias1',
                'warner_player_type' => PlayerIdentifierType::MINECRAFT_UUID->value,
                'warner_player_id' => $player2->uuid,
                'warner_player_alias' => 'alias2',
                'reason' => 'reason',
                'weight' => 5,
            ])
            ->assertSuccessful();

        $this->assertDatabaseHas(
            table: PlayerWarning::getTableName(),
            data: [
                'warned_player_id' => $player1->getKey(),
                'warner_player_id' => $player2->getKey(),
                'reason' => 'reason',
                'weight' => 5,
                'created_at' => $this->now,
                'updated_at' => $this->now,
            ],
        );
    }
}
