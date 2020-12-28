<?php
namespace Tests;

use Tests\TestCase;
use App\Entities\EnvironmentLevel;
use App\Entities\Environment;

class Environment_Test extends TestCase
{
    private $originalEnv;

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


    public function testCanGetLevel()
    {
        // given...
        config()->set('app.env', EnvironmentLevel::ENV_PRODUCTION);

        // when...
        $environment = Environment::getLevel();

        // expect...
        $this->assertEquals(EnvironmentLevel::ENV_PRODUCTION, $environment->valueOf());
    }

    public function testCanOverrideLevel()
    {
        // given...
        $level = new EnvironmentLevel(EnvironmentLevel::ENV_STAGING);
        Environment::overrideLevel($level);

        // when...
        $environment = Environment::getLevel();

        // expect...
        $this->assertEquals(EnvironmentLevel::ENV_STAGING, $environment->valueOf());
    }

    public function testIsProduction_whenProduction()
    {
        // given...
        config()->set('app.env', EnvironmentLevel::ENV_PRODUCTION);
        
        // expect...
        $this->assertTrue(Environment::isProduction());
    }

    public function testIsProduction_whenStaging()
    {
        // given...
        config()->set('app.env', EnvironmentLevel::ENV_STAGING);
        
        // expect...
        $this->assertFalse(Environment::isProduction());
    }


}
