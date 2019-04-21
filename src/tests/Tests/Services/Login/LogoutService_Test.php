<?php
namespace Tests\Services;

use Tests\TestCase;
use Domains\Library\Discourse\Api\DiscourseUserApi;
use Domains\Library\Discourse\Api\DiscourseAdminApi;
use Entities\Accounts\Models\Account;
use Domains\Services\Login\LogoutService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Contracts\Auth\Guard as Auth;
use Illuminate\Log\Logger;

class LogoutService_Test extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;

    private $loggerStub;
    private $discourseUserApiMock;
    private $discourseAdminApiMock;

    public function setUp()
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
        // given...
        $auth = resolve(Auth::class);
        $service = new LogoutService($this->discourseUserApiMock, $this->discourseAdminApiMock, $auth, $this->loggerStub);
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
        $service = new LogoutService($this->discourseUserApiMock, $this->discourseAdminApiMock, $auth, $this->loggerStub);

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
        $service = new LogoutService($this->discourseUserApiMock, $this->discourseAdminApiMock, $auth, $this->loggerStub);
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
        $service = new LogoutService($this->discourseUserApiMock, $this->discourseAdminApiMock, $auth, $this->loggerStub);
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
