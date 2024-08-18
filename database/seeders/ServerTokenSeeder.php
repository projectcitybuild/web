<?php

namespace Database\Seeders;

use App\Core\Domains\Tokens\Adapters\HashedTokenGenerator;
use App\Models\Server;
use App\Models\ServerToken;
use App\Models\ServerTokenScope;
use Domain\ServerTokens\ScopeKey;
use Illuminate\Database\Seeder;

class ServerTokenSeeder extends Seeder
{
    public function run()
    {
        $scopes = collect(ScopeKey::values())->map(
            fn ($scope) => ServerTokenScope::create(['scope' => $scope])->getKey(),
        );

        $token = ServerToken::create([
            'token' => (new HashedTokenGenerator())->make(),
            'server_id' => Server::first()->getKey(),
            'description' => 'For test use',
        ]);

        $token->scopes()->sync($scopes);
    }
}
