<?php

namespace Tests\Unit\App\Core\Domains\Environment;

use App\Core\Domains\Environment\Environment;
use App\Core\Domains\Environment\EnvironmentLevel;
use Tests\Support\TemporaryConfig;

uses(TemporaryConfig::class);

it('returns the configured environment level', function () {
    collect(EnvironmentLevel::cases())->each(function ($environmentLevel) {
        $this->setTemporaryConfig('app.env', $environmentLevel->value);

        $this->assertEquals(
            expected: $environmentLevel,
            actual: Environment::getLevel()
        );
    });
});

it('identifies production environments', function () {
    collect(EnvironmentLevel::cases())->each(function ($environmentLevel) {
        $this->setTemporaryConfig('app.env', $environmentLevel->value);

        $this->assertEquals(
            expected: $environmentLevel == EnvironmentLevel::PRODUCTION,
            actual: Environment::isProduction(),
        );
    });
});

it('identifies staging environments', function () {
    collect(EnvironmentLevel::cases())->each(function ($environmentLevel) {
        $this->setTemporaryConfig('app.env', $environmentLevel->value);

        $this->assertEquals(
            expected: $environmentLevel == EnvironmentLevel::STAGING,
            actual: Environment::isStaging(),
        );
    });
});

it('identifies testing environments', function () {
    collect(EnvironmentLevel::cases())->each(function ($environmentLevel) {
        $this->setTemporaryConfig('app.env', $environmentLevel->value);

        $this->assertEquals(
            expected: $environmentLevel == EnvironmentLevel::TESTING,
            actual: Environment::isTest(),
        );
    });
});

it('identifies development environments', function () {
    collect(EnvironmentLevel::cases())->each(function ($environmentLevel) {
        $this->setTemporaryConfig('app.env', $environmentLevel->value);

        $this->assertEquals(
            expected: $environmentLevel == EnvironmentLevel::DEVELOPMENT,
            actual: Environment::isLocalDev(),
        );
    });
});
