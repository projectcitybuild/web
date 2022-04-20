<?php

namespace Tests\Unit\Shared\ExternalAccounts;

use Entities\Models\Eloquent\Account;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Library\Discourse\Api\DiscourseAdminApi;
use Library\Discourse\Api\DiscourseUserApi;
use Library\Discourse\Authentication\DiscourseLoginHandler;
use Library\Discourse\Entities\DiscoursePackedNonce;
use Psr\Http\Message\RequestInterface;
use Shared\ExternalAccounts\Session\Adapters\DiscourseAccountSession;
use Tests\TestCase;

class DiscourseAccountSessionTests extends TestCase
{
    use RefreshDatabase;

    private DiscourseAdminApi $discourseAdminAPI;
    private DiscourseUserApi $discourseUserAPI;
    private DiscourseLoginHandler $discourseLoginHandler;
    private DiscourseAccountSession $discourseAccountSession;

    public function setUp(): void
    {
        parent::setUp();

        $this->discourseAdminAPI = \Mockery::mock(DiscourseAdminApi::class);
        $this->discourseUserAPI = \Mockery::mock(DiscourseUserApi::class);
        $this->discourseLoginHandler = \Mockery::mock(DiscourseLoginHandler::class);

        $this->discourseAccountSession = new DiscourseAccountSession(
            discourseUserAPI: $this->discourseUserAPI,
            discourseAdminAPI: $this->discourseAdminAPI,
            discourseLoginHandler: $this->discourseLoginHandler,
        );
    }

    public function test_login_without_nonce()
    {
        $account = Account::factory()->create();
        $expectedRedirect = redirect()->to('url');

        $this->discourseLoginHandler
            ->shouldReceive('getRedirectUrl')
            ->andReturn($expectedRedirect);

        $redirect = $this->discourseAccountSession->login($account);

        $this->assertEquals(
            expected: $expectedRedirect,
            actual: $redirect,
        );
    }

    public function test_login_with_nonce()
    {
        $account = Account::factory()->create();
        $nonce = new DiscoursePackedNonce('sso', 'sig');
        $expectedRedirect = redirect()->to('url');

        $this->discourseLoginHandler
            ->shouldReceive('getRedirectUrl')
            ->andReturn('url');

        $redirect = $this->discourseAccountSession->login($account, $nonce);

        $this->assertEquals(
            expected: $expectedRedirect,
            actual: $redirect,
        );
    }

    public function test_logout_requests_logout()
    {
        $account = Account::factory()->create();
        $discourseAccountId = 1234;

        $this->discourseUserAPI
            ->shouldReceive('fetchUserByPcbId')
            ->with($account->getKey())
            ->andReturn([
                'user' => [
                    'id' => $discourseAccountId,
                    'username' => 'xxx',
                ],
            ]);

        $this->discourseAdminAPI
            ->shouldReceive('requestLogout')
            ->with($discourseAccountId);

        $this->discourseAccountSession->logout($account->getKey());
    }

    public function test_logout_ignores_discourse_404_errors()
    {
        $account = Account::factory()->create();
        $discourseAccountId = 1234;

        $this->discourseUserAPI
            ->shouldReceive('fetchUserByPcbId')
            ->with($account->getKey())
            ->andReturn([
                'user' => [
                    'id' => $discourseAccountId,
                    'username' => 'xxx',
                ],
            ]);

        $this->discourseAdminAPI
            ->shouldReceive('requestLogout')
            ->with($discourseAccountId)
            ->andThrow(
                new ClientException(
                    message: 'message',
                    request: \Mockery::mock(RequestInterface::class),
                    response: new Response(status: 404),
                )
            );

        $this->discourseAccountSession->logout($account->getKey());
    }
}
