<?php

namespace Tests\Support;

class JsonFixture
{
    public static function loadFromStorage(string $path): array
    {
        $jsonFilePath = storage_path('testing/'.$path);
        $json = file_get_contents($jsonFilePath);

        return json_decode($json, associative: true);
    }
}
