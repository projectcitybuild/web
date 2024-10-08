<?php

namespace Database\Seeders;

use App\Core\Domains\SecureTokens\Adapters\HashedSecureTokenGenerator;
use App\Models\Server;
use App\Models\ServerToken;
use Illuminate\Database\Seeder;

class ServerTokenSeeder extends Seeder
{
    public function run()
    {
        ServerToken::create([
            'token' => (new HashedSecureTokenGenerator())->make(),
            'server_id' => Server::first()->getKey(),
            'description' => 'For test use',
        ]);
    }
}
