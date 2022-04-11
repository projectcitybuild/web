<?php

namespace Tests\Services;

use App\Entities\Models\Eloquent\Account;
use Domain\Login\UseCases\LogoutUseCase;
use Illuminate\Contracts\Auth\Guard as Auth;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Log\Logger;
use Library\Discourse\Api\DiscourseAdminApi;
use Library\Discourse\Api\DiscourseUserApi;
use Tests\TestCase;

class LogoutService_Test extends TestCase
{
    use RefreshDatabase;

    private $loggerStub;
    private $discourseUserApiMock;
    private $discourseAdminApiMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->loggerStub = $this->getMockBuilder(Logger::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->discourseUserApiMock = $this->getMockBuilder(DiscourseUserApi::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->discourseAdminApiMock = $this->getMockBuilder(DiscourseAdminApi::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Creates a new Account and logins
     * as that account.
     *
     * @return void
     */
    private function loginAsUser(Auth $auth)
    {
        $account = Account::factory()->create();
        $auth->setUser($account);

        $this->assertTrue($auth->check());
    }

    public function testLogoutOfPcb_canLogout()
    {
        // given...
        $auth = resolve(Auth::class);
        $service = new LogoutUseCase($this->discourseUserApiMock, $this->discourseAdminApiMock, $auth, $this->loggerStub);
        $this->loginAsUser($auth);

        // when...
        $service->logoutOfPCB();

        // expect...
        $this->assertFalse($auth->check());
    }

    public function testLogoutOfPcb_notLoggedIn_doesNothing()
    {
        // given...
        $auth = resolve(Auth::class);
        $service = new LogoutUseCase($this->discourseUserApiMock, $this->discourseAdminApiMock, $auth, $this->loggerStub);

        $this->assertFalse($auth->check());

        // when...
        $result = $service->logoutOfPCB();

        // expect...
        $this->assertFalse($result);
    }

    public function testLogoutOfBoth_canLogoutOfPcb()
    {
        // given...
        $this->discourseUserApiMock
            ->expects($this->once())
            ->method('fetchUserByPcbId')
            ->willReturn([
                'user' => [
                    'id' => 15,
                    'username' => 'test_user',
                ],
            ]);

        $auth = resolve(Auth::class);
        $service = new LogoutUseCase($this->discourseUserApiMock, $this->discourseAdminApiMock, $auth, $this->loggerStub);
        $this->loginAsUser($auth);

        // when...
        $service->logoutOfDiscourseAndPcb();

        // expect...
        $this->assertFalse($auth->check());
    }

    public function testLogoutOfBoth_usesCorrectDiscourseId()
    {
        // given...
        $discourseId = 111;

        $this->discourseUserApiMock
            ->expects($this->once())
            ->method('fetchUserByPcbId')
            ->willReturn([
                'user' => [
                    'id' => $discourseId,
                    'username' => 'test_user',
                ],
            ]);

        $auth = resolve(Auth::class);
        $service = new LogoutUseCase($this->discourseUserApiMock, $this->discourseAdminApiMock, $auth, $this->loggerStub);
        $this->loginAsUser($auth);

        // expect...
        $this->discourseAdminApiMock
            ->expects($this->once())
            ->method('requestLogout')
            ->with($this->equalTo($discourseId));

        // when...
        $service->logoutOfDiscourseAndPcb();
    }
}
