<?php
namespace Tests;

use Tests\TestCase;
use Application\EnvironmentLevel;
use Application\Environment;

class Environment_Test extends TestCase
{
    private $originalEnv;

    protected function setUp()
    {
        parent::setUp();

        $this->originalEnv = config('app.env');
        Environment::resetLevel();
    }

    protected function tearDown()
    {
        config(['app.env' => $this->originalEnv]);
        
        parent::tearDown();
    }


    public function testCanGetLevel()
    {
        // given...
        config(['app.env' => EnvironmentLevel::Production]);

        // when...
        $environment = Environment::getLevel();

        // expect...
        $this->assertEquals(EnvironmentLevel::Production, $environment->valueOf());
    }

    public function testCanOverrideLevel_withEnum()
    {
        // given...
        $level = new EnvironmentLevel(EnvironmentLevel::Staging);
        Environment::overrideLevel($level);

        // when...
        $environment = Environment::getLevel();

        // expect...
        $this->assertEquals(EnvironmentLevel::Staging, $environment->valueOf());
    }

    public function testCanOverrideLevel_withString()
    {
        // given...
        Environment::overrideLevel(EnvironmentLevel::Staging);

        // when...
        $environment = Environment::getLevel();

        // expect...
        $this->assertEquals(EnvironmentLevel::Staging, $environment->valueOf());
    }

    public function testIsProduction_whenProduction()
    {
        // given...
        config(['app.env' => EnvironmentLevel::Production]);
        
        // expect...
        $this->assertTrue(Environment::isProduction());
    }

    public function testIsProduction_whenStaging()
    {
        // given...
        config(['app.env' => EnvironmentLevel::Staging]);
        
        // expect...
        $this->assertFalse(Environment::isProduction());
    }


}
