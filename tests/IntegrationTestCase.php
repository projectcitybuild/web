<?php

namespace Tests;

use App\Models\ServerToken;
use Illuminate\Foundation\Testing\RefreshDatabase;

/** @deprecated Inherit TestCase instead */
abstract class IntegrationTestCase extends TestCase
{
    use RefreshDatabase;

    protected ?ServerToken $token = null;

    /**
     * Returns the contents of a JSON file inside the `resources/testing` folder
     *
     * @param  string  $path  Relative path to the file from the storage folder
     * @return array File contents as an associative array
     */
    protected function loadJsonFromFile(string $path): array
    {
        $jsonFilePath = storage_path('testing/'.$path);
        $json = file_get_contents($jsonFilePath);

        return json_decode($json, associative: true);
    }
}
