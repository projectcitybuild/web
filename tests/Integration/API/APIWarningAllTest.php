<?php

namespace Tests\Integration\API;

use App\Domains\ServerTokens\ScopeKey;
use App\Models\MinecraftPlayer;
use App\Models\PlayerWarning;
use Entities\Models\PlayerIdentifierType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\IntegrationTestCase;

class APIWarningAllTest extends IntegrationTestCase
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
            'player_type' => PlayerIdentifierType::MINECRAFT_UUID->value,
            'player_id' => 'uuid1',
            'player_alias' => 'alias1',
        ];
    }

    public function test_requires_scope()
    {
        $this->withAuthorizationServerToken()
            ->getJson(uri: self::ENDPOINT.'?'.http_build_query($this->validData()))
            ->assertForbidden();

        $this->authoriseTokenFor(ScopeKey::WARNING_LOOKUP);

        $this->withAuthorizationServerToken()
            ->getJson(uri: self::ENDPOINT.'?'.http_build_query($this->validData()))
            ->assertSuccessful();
    }

    public function test_shows_warnings()
    {
        $this->authoriseTokenFor(ScopeKey::WARNING_LOOKUP);

        $player = MinecraftPlayer::factory()->create();

        $warning1 = PlayerWarning::factory()
            ->for($player, 'warnedPlayer')
            ->for(MinecraftPlayer::factory(), 'warnerPlayer')
            ->createdAt(now())
            ->create();

        $warning2 = PlayerWarning::factory()
            ->for($player, 'warnedPlayer')
            ->for(MinecraftPlayer::factory(), 'warnerPlayer')
            ->createdAt(now()->subDay())
            ->create();

        $this->withAuthorizationServerToken()
            ->getJson(uri: self::ENDPOINT.'?'.http_build_query([
                'player_type' => PlayerIdentifierType::MINECRAFT_UUID->value,
                'player_id' => $player->uuid,
                'player_alias' => 'alias1',
            ]))
            ->assertJson([
                'data' => [
                    [
                        'id' => $warning1->getKey(),
                        'warned_player_id' => $warning1->warned_player_id,
                        'warner_player_id' => $warning1->warner_player_id,
                        'reason' => $warning1->reason,
                        'weight' => $warning1->weight,
                        'is_acknowledged' => $warning1->is_acknowledged,
                        'created_at' => $warning1->created_at->timestamp,
                        'updated_at' => $warning1->updated_at->timestamp,
                    ],
                    [
                        'id' => $warning2->getKey(),
                        'warned_player_id' => $warning2->warned_player_id,
                        'warner_player_id' => $warning2->warner_player_id,
                        'reason' => $warning2->reason,
                        'weight' => $warning2->weight,
                        'is_acknowledged' => $warning2->is_acknowledged,
                        'created_at' => $warning2->created_at->timestamp,
                        'updated_at' => $warning2->updated_at->timestamp,
                    ],
                ],
            ])
            ->assertSuccessful();
    }

    public function test_shows_no_warnings()
    {
        $this->authoriseTokenFor(ScopeKey::WARNING_LOOKUP);

        $player = MinecraftPlayer::factory()->create(['uuid' => 'uuid1']);

        $this->withAuthorizationServerToken()
            ->getJson(uri: self::ENDPOINT.'?'.http_build_query([
                'player_type' => PlayerIdentifierType::MINECRAFT_UUID->value,
                'player_id' => $player->uuid,
                'player_alias' => 'alias1',
            ]))
            ->assertJson(['data' => []])
            ->assertSuccessful();
    }
}
