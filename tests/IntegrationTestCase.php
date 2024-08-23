<?php

namespace Tests;

use App\Domains\Panel\Data\PanelGroupScope;
use App\Domains\ServerTokens\ScopeKey;
use App\Models\Account;
use App\Models\Group;
use App\Models\GroupScope;
use App\Models\Server;
use App\Models\ServerCategory;
use App\Models\ServerToken;
use App\Models\ServerTokenScope;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

abstract class IntegrationTestCase extends TestCase
{
    use RefreshDatabase;

    protected Carbon $now;
    protected ?ServerToken $token = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->now = $this->setTestNow();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        Carbon::setTestNow(); // Reset
    }

    /**
     * Returns the contents of a JSON file inside the `resources/testing` folder
     *
     * @param  string  $path Relative path to the file from the storage folder
     * @return array File contents as an associative array
     */
    protected function loadJsonFromFile(string $path): array
    {
        $jsonFilePath = storage_path('testing/'.$path);
        $json = file_get_contents($jsonFilePath);

        return json_decode($json, associative: true);
    }

    protected function createServerToken(): ServerToken
    {
        $server = Server::factory()->create();
        $this->token = ServerToken::factory()->create(['server_id' => $server->getKey()]);

        return $this->token;
    }

    protected function withAuthorizationServerToken(): TestCase
    {
        return $this->withHeader(
            name: 'Authorization',
            value: 'Bearer '.$this->token->token,
        );
    }

    protected function authoriseTokenFor(ScopeKey ...$scopes)
    {
        foreach ($scopes as $scope) {
            $tokenScope = ServerTokenScope::create(['scope' => $scope->value]);
            $this->token->scopes()->attach($tokenScope->getKey());
        }
    }

    /**
     * Get a user in a group with admin rights.
     *
     * @param  PanelGroupScope[]  $scopes abilities to give to the user's group
     */
    protected function adminAccount(array $scopes = []): Account
    {
        $group = Group::factory()->administrator()->create();
        $group->groupScopes()->attach(
            collect($scopes)
                ->map(fn ($case) => GroupScope::factory()->create(['scope' => $case->value]))
                ->map(fn ($model) => $model->getKey())
        );
        $account = Account::factory()
            ->hasFinishedTotp()
            ->create();

        $account->groups()->attach($group->getKey());

        return $account;
    }
}
