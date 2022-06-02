<?php

namespace Tests\E2E\API;

use Domain\ServerTokens\ScopeKey;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\E2ETestCase;

class APIMinecraftTelemetryTest extends E2ETestCase
{
    use RefreshDatabase;

    private const ENDPOINT = 'api/v2/telemetry/minecraft/seen';

    protected function setUp(): void
    {
        parent::setUp();

        $this->createServerToken();
    }

    public function test_requires_scope()
    {
        $this->withAuthorizationServerToken()
            ->getJson(self::ENDPOINT, ['uuid' => 'uuid', 'alias' => 'alias'])
            ->assertUnauthorized();

        $this->authoriseTokenFor(ScopeKey::TELEMETRY);

        $this->withAuthorizationServerToken()
            ->getJson(self::ENDPOINT, ['uuid' => 'uuid', 'alias' => 'alias'])
            ->assertOk();
    }
}
