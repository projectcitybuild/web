<?php

namespace Tests\Unit\Shared\ExternalAccounts;

use App\Entities\Models\Eloquent\Account;
use App\Entities\Models\Eloquent\Group;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Library\Discourse\Api\DiscourseAdminApi;
use Psr\Http\Message\RequestInterface;
use Shared\ExternalAccounts\Sync\Adapters\DiscourseAccountSync;
use Tests\TestCase;

class DiscourseAccountSyncTests extends TestCase
{
    use RefreshDatabase;

    private DiscourseAdminApi $discourseAdminApi;
    private DiscourseAccountSync $discourseAccountSync;

    public function setUp(): void
    {
        parent::setUp();

        $this->discourseAdminApi = \Mockery::mock(DiscourseAdminApi::class);
        $this->discourseAccountSync = new DiscourseAccountSync(
            discourseAdminApi: $this->discourseAdminApi,
        );
    }

    public function test_sends_payload_to_discourse_api()
    {
        $group1 = Group::factory()->create();
        $group2 = Group::factory()->create();

        $account = Account::factory()->create();
        $account->groups()->attach($group1);
        $account->groups()->attach($group2);

        $this->discourseAdminApi
            ->shouldReceive('requestSSOSync')
            ->with([
                'email' => $account->email,
                'external_id' => $account->getKey(),
                'username' => $account->username,
                'groups' => $group1->discourse_name . ',' . $group2->discourse_name,
                "require_activation" => true,
            ]);

        $this->discourseAccountSync->sync($account);
    }

    public function test_sends_payload_without_require_activation_on_retry()
    {
        $account = Account::factory()->create();

        $expectedPayload = [
            'email' => $account->email,
            'external_id' => $account->getKey(),
            'username' => $account->username,
            'groups' => '',
        ];

        $this->discourseAdminApi
            ->shouldReceive('requestSSOSync')
            ->once()
            ->with(array_merge($expectedPayload, ['require_activation' => true]))
            ->andThrow(
                new ServerException(
                    message: "message",
                    request: \Mockery::mock(RequestInterface::class),
                    response: new Response(status: 500),
                )
            );

        $this->discourseAdminApi
            ->shouldReceive('requestSSOSync')
            ->once()
            ->with(array_merge($expectedPayload, ['require_activation' => false]));

        $this->discourseAccountSync->sync($account);
    }
}
