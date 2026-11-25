<?php

use Database\Seeders\OAuthClientSeeder;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Client;

it('creates one active Discord client credentials client with a hashed secret', function () {
    $seeder = new OAuthClientSeeder;

    $seeder->run();
    $seeder->run();

    $client = Client::query()->where('name', 'Discord')->sole();

    $this->assertModelExists($client);

    expect($client->grant_types)->toBe(['client_credentials'])
        ->and($client->revoked)->toBeFalse()
        ->and(Hash::check('discord-client-secret', $client->secret))->toBeTrue();
});
