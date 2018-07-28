<?php
namespace Tests\Tests\Services\Login;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Domains\Services\Login\LogoutService;
use Domains\Library\Discourse\Api\DiscourseUserApi;
use Domains\Library\Discourse\Api\DiscourseAdminApi;

class LogoutService_Test extends TestCase
{
    public function testLogoutOfPcb_succeeds()
    {
        $userApiMock = $this->getMockBuilder(DiscourseUserApi::class)
            ->getMock();

        $userApiMock->expects($this->any())
            ->method('fetchUserByPcbId')
            ->willReturn([

            ]);

        $adminApiMock = $this->getMockBuilder(DiscourseAdminApi::class)
            ->getMock();

        $adminApiMock->expects($this->any())
            ->method('requestLogout')
            ->willReturn();

        $service = new LogoutService($userApiMock, 
                                     $adminApiMock, 
                                     $authMock, 
                                     $loggerMock);

        $service->logoutOfPCB();
        
    }
}
