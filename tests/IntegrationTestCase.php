<?php

namespace Tests;

use App\Domains\Manage\Data\PanelGroupScope;
use App\Models\Account;
use App\Models\Group;
use App\Models\GroupScope;
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
     * @param  string  $path Relative path to the file from the storage folder
     * @return array File contents as an associative array
     */
    protected function loadJsonFromFile(string $path): array
    {
        $jsonFilePath = storage_path('testing/'.$path);
        $json = file_get_contents($jsonFilePath);

        return json_decode($json, associative: true);
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
