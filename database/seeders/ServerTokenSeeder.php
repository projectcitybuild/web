<?php

namespace Database\Seeders;

use Domain\ServerTokens\ScopeKey;
use Entities\Models\Eloquent\Server;
use Entities\Models\Eloquent\ServerToken;
use Entities\Models\Eloquent\ServerTokenScope;
use Illuminate\Database\Seeder;
use Library\Tokens\Adapters\HashedTokenGenerator;

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
