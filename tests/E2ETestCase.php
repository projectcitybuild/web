<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Shared\ExternalAccounts\Sync\Adapters\StubAccountSync;
use Shared\ExternalAccounts\Sync\ExternalAccountSync;

abstract class E2ETestCase extends TestCase
{
    use RefreshDatabase;

    protected Carbon $now;

    protected function setUp(): void
    {
        parent::setUp();

        $this->now = $this->setTestNow();

        // Prevent calls to Discourse
        $this->app->bind(ExternalAccountSync::class, StubAccountSync::class);
    }

    /**
     * Returns the contents of a JSON file inside the `resources/testing` folder
     *
     * @param string $path Relative path to the file from the storage folder
     * @return array File contents as an associative array
     */
    protected function loadJsonFromFile(string $path): array
    {
        $jsonFilePath = storage_path('testing/' . $path);
        $json = file_get_contents($jsonFilePath);

        return json_decode($json, associative: true);
    }
}
