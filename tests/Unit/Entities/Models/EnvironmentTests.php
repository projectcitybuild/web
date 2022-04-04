<?php

namespace Tests\Unit\Entities\Models;

use App\Entities\Models\Environment;
use App\Entities\Models\EnvironmentLevel;
use Tests\TestCase;

class EnvironmentTests extends TestCase
{
    private string $originalEnv;

    protected function setUp(): void
    {
        parent::setUp();

        $this->originalEnv = config('app.env');
        Environment::resetLevel();
    }

    protected function tearDown(): void
    {
        config()->set('app.env', $this->originalEnv);

        parent::tearDown();
    }

    public function test_can_get_level()
    {
        collect(EnvironmentLevel::cases())->each(function ($environment) {
            config()->set('app.env', $environment);

            $this->assertEquals(
                expected: $environment,
                actual: Environment::getLevel()
            );
        });
    }

    public function test_can_override_level()
    {
        collect(EnvironmentLevel::cases())->each(function ($environment) {
            config()->set('app.env', $environment);

            Environment::overrideLevel(EnvironmentLevel::ENV_STAGING);

            $this->assertEquals(
                expected: EnvironmentLevel::ENV_STAGING,
                actual: Environment::getLevel()
            );
        });
    }

    public function test_is_production_when_production()
    {
        config()->set('app.env', EnvironmentLevel::ENV_PRODUCTION->value);

        $this->assertTrue(Environment::isProduction());
    }

    public function test_not_production_when_staging()
    {
        config()->set('app.env', EnvironmentLevel::ENV_STAGING->value);

        $this->assertFalse(Environment::isProduction());
    }
}
