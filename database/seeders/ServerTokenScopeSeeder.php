<?php

namespace Database\Seeders;

use Domain\ServerTokens\ScopeKey;
use Entities\Models\Eloquent\ServerTokenScope;
use Illuminate\Database\Seeder;

class ServerTokenScopeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        collect(ScopeKey::values())->each(
            fn ($scope) => ServerTokenScope::create(['scope' => $scope]),
        );
    }
}
