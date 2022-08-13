<?php

namespace Tests\Support;

use Illuminate\Support\Collection;
use RuntimeException;

/**
 * Modify application config and reset on teardown
 */
trait TemporaryConfig
{
    private Collection $originalValues;

    /**
     * Initialise the store of original config values
     * Run automatically by the base test case
     *
     * @return void
     *
     * @throws RuntimeException if temporary values were not properly restored last time
     */
    public function setUpTemporaryConfig(): void
    {
        if (isset($this->originalValues) && $this->originalValues->isNotEmpty()) {
            throw new RuntimeException('Attempted to setup whilst temporary config array still had content');
        }

        $this->originalValues = collect();
    }

    /**
     * Reset configuration to default
     * Run automatically by the base test case
     *
     * @return void
     */
    public function tearDownTemporaryConfig(): void
    {
        $this->resetAllValues();
    }

    private function resetAllValues(): void
    {
        $this->originalValues->each(function ($item, $key) {
            $this->resetValue($key);
        });
    }

    /**
     * Set a temporary configuration value for this test
     *
     * @param $key string the configuration key
     * @param $value mixed the configuration value
     * @return void
     */
    public function setTemporaryConfig(string $key, mixed $value): void
    {
        if (! config()->has($key)) {
            throw new RuntimeException("Config item `$key` could not be set as it does not exist");
        }

        if (! isset($this->originalValues)) {
            throw new RuntimeException('Attempted to set temporary config value, but trait was not initialised.');
        }

        if ($this->hasNotBeenModified($key)) {
            $currentValue = config($key);
            $this->originalValues->put($key, $currentValue);
        }

        config([$key => $value]);
    }

    private function hasNotBeenModified($key): bool
    {
        return $this->originalValues->has($key);
    }

    private function resetValue($key): void
    {
        config([$key => $this->originalValues->pull($key)]);
    }
}
