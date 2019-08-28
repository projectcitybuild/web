<?php

namespace Tests\Services;

use Tests\TestCase;
use App\Library\Discourse\Api\DiscourseUserApi;
use App\Library\Discourse\Api\DiscourseAdminApi;
use App\Entities\Eloquent\Accounts\Models\Account;
use App\Services\Login\LogoutService;
use Illuminate\Contracts\Auth\Guard as Auth;
use Illuminate\Log\Logger;
use Illuminate\Foundation\Testing\RefreshDatabase;

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
     * as that account
     *
     * @param Auth $auth
     * @return void
     */
    private function loginAsUser(Auth $auth)
    {
        $account = factory(Account::class)->create();
        $auth->setUser($account);

        $this->assertTrue($auth->check());
    }

    public function testLogoutOfPcb_canLogout()
    {
        $auth = resolve(Auth::class);
        $service = new LogoutService($this->discourseUserApiMock, $this->discourseAdminApiMock, $auth, $this->loggerStub);
        $this->loginAsUser($auth);

        $service->logoutOfPCB();

        $this->assertFalse($auth->check());
    }
    
    public function testLogoutOfPcb_notLoggedIn_doesNothing()
    {
        $auth = resolve(Auth::class);
        $service = new LogoutService($this->discourseUserApiMock, $this->discourseAdminApiMock, $auth, $this->loggerStub);

        $this->assertFalse($auth->check());

        $result = $service->logoutOfPCB();

        $this->assertFalse($result);
    }

    public function testLogoutOfBoth_canLogoutOfPcb()
    {
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
        $service = new LogoutService($this->discourseUserApiMock, $this->discourseAdminApiMock, $auth, $this->loggerStub);
        $this->loginAsUser($auth);

        $service->logoutOfDiscourseAndPcb();

        $this->assertFalse($auth->check());
    }

    public function testLogoutOfBoth_usesCorrectDiscourseId()
    {
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
        $service = new LogoutService($this->discourseUserApiMock, $this->discourseAdminApiMock, $auth, $this->loggerStub);
        $this->loginAsUser($auth);

        $this->discourseAdminApiMock
            ->expects($this->once())
            ->method('requestLogout')
            ->with($this->equalTo($discourseId));

        $service->logoutOfDiscourseAndPcb();
    }
}
