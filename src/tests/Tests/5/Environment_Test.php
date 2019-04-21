<?php
namespace Tests;

use Tests\TestCase;
use App\EnvironmentLevel;
use App\Environment;

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
        config()->set('app.env', EnvironmentLevel::Production);

        // when...
        $environment = Environment::getLevel();

        // expect...
        $this->assertEquals(EnvironmentLevel::Production, $environment->valueOf());
    }

    public function testCanOverrideLevel()
    {
        // given...
        $level = EnvironmentLevel::Staging();
        Environment::overrideLevel($level);

        // when...
        $environment = Environment::getLevel();

        // expect...
        $this->assertEquals(EnvironmentLevel::Staging, $environment->valueOf());
    }

    public function testIsProduction_whenProduction()
    {
        // given...
        config()->set('app.env', EnvironmentLevel::Production);
        
        // expect...
        $this->assertTrue(Environment::isProduction());
    }

    public function testIsProduction_whenStaging()
    {
        // given...
        config()->set('app.env', EnvironmentLevel::Staging);
        
        // expect...
        $this->assertFalse(Environment::isProduction());
    }


}
