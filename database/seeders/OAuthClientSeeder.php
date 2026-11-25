<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Laravel\Passport\Client;

class OAuthClientSeeder extends Seeder
{
    private const SECRET = 'discord-client-secret';

    public function run(): void
    {
        Client::query()->updateOrCreate(
            ['name' => 'Discord'],
            [
                'secret' => self::SECRET,
                'grant_types' => ['client_credentials'],
                'redirect_uris' => [],
                'revoked' => false,
            ],
        );
    }
}
