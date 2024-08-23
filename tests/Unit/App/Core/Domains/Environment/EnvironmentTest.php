<?php

namespace App\Core\Domains\Environment;

use Tests\Support\TemporaryConfig;
use Tests\TestCase;

class EnvironmentTest extends TestCase
{
    use TemporaryConfig;

    public function test_can_get_level()
    {
        collect(EnvironmentLevel::cases())->each(function ($environmentLevel) {
            $this->setTemporaryConfig('app.env', $environmentLevel->value);

            $this->assertEquals(
                expected: $environmentLevel,
                actual: Environment::getLevel()
            );
        });
    }

    public function test_is_production_level()
    {
        collect(EnvironmentLevel::cases())->each(function ($environmentLevel) {
            $this->setTemporaryConfig('app.env', $environmentLevel->value);

            $this->assertEquals(
                expected: $environmentLevel == EnvironmentLevel::PRODUCTION,
                actual: Environment::isProduction(),
            );
        });
    }

    public function test_is_staging_level()
    {
        collect(EnvironmentLevel::cases())->each(function ($environmentLevel) {
            $this->setTemporaryConfig('app.env', $environmentLevel->value);

            $this->assertEquals(
                expected: $environmentLevel == EnvironmentLevel::STAGING,
                actual: Environment::isStaging(),
            );
        });
    }

    public function test_is_testing_level()
    {
        collect(EnvironmentLevel::cases())->each(function ($environmentLevel) {
            $this->setTemporaryConfig('app.env', $environmentLevel->value);

            $this->assertEquals(
                expected: $environmentLevel == EnvironmentLevel::TESTING,
                actual: Environment::isTest(),
            );
        });
    }

    public function test_is_local_dev_level()
    {
        collect(EnvironmentLevel::cases())->each(function ($environmentLevel) {
            $this->setTemporaryConfig('app.env', $environmentLevel->value);

            $this->assertEquals(
                expected: $environmentLevel == EnvironmentLevel::DEVELOPMENT,
                actual: Environment::isLocalDev(),
            );
        });
    }
}
