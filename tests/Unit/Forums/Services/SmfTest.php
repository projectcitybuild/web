<?php
namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Modules\Forums\Services\SMF\{Smf, SmfUserFactory, SmfUser};
use App\Modules\Forums\Repositories\ForumUserRepository;
use App\Modules\Forums\Models\ForumUser;

class SmfTest extends TestCase {

    private $repositoryMock;
    private $factoryMock;
    private $smfUserMock;

    public function setUp() {
        parent::setUp();
        
        $modelMock = $this->getMockBuilder(ForumUser::class)
            ->getMock();

        $this->repositoryMock = $this->getMockBuilder(ForumUserRepository::class)
            ->setConstructorArgs([$modelMock])
            ->getMock();

        $this->factoryMock = $this->getMockBuilder(SmfUserFactory::class)
            ->setConstructorArgs([$this->repositoryMock, [1, 2, 3]])
            ->getMock();

        $this->smfUserMock = $this->getMockBuilder(SmfUser::class)
            ->setConstructorArgs([1, $this->repositoryMock, [1, 2, 3]])
            ->getMock();
    }

    /**
     * Tests that no cookie present will still call the factory getInstance()
     * with a guest id
     *
     * @return void
     */
    public function testGetUser_whenNoCookie_getsInstanceFromFactory() {
        $this->factoryMock->expects($this->once())
            ->method('getInstance')
            ->with($this->equalTo(-1))
            ->will($this->returnValue($this->smfUserMock));

        $test = $this->getMockBuilder(Smf::class)
            ->setConstructorArgs([$this->repositoryMock, '', $this->factoryMock])
            ->setMethods(['getSmfCookie'])
            ->getMock();

        $test->expects($this->once())
            ->method('getSmfCookie')
            ->will($this->returnValue(null));

        $result = $test->getUser();

        $this->assertNotNull($result);
    }

    /**
     * Tests that a valid cookie will call the factory getInstance()
     * with a non-guest id
     *
     * @return void
     */
    // public function testGetUser_whenValidCookie_getsInstanceFromFactory() {
    //     $fakeUser = new \StdClass();
    //     $fakeUser->passwd = 'password';
    //     $fakeUser->password_salt = 'salt';

    //     $this->modelMock->expects($this->once())
    //         ->method('find')
    //         ->will($this->returnValue($fakeUser));

    //     $this->factoryMock->expects($this->once())
    //         ->method('getInstance')
    //         ->with($this->equalTo(1))
    //         ->will($this->returnValue($this->smfUserMock));

    //     $test = $this->getMockBuilder(Smf::class)
    //         ->setConstructorArgs([$this->modelMock, '', $this->factoryMock])
    //         ->setMethods(['getSmfCookie'])
    //         ->getMock();

    //     // sha1 hash of 'passwordsalt' = c88e9c67041a74e0357befdff93f87dde0904214
    //     $validCookie = serialize([1, 'c88e9c67041a74e0357befdff93f87dde0904214', null]);

    //     $test->expects($this->once())
    //         ->method('getSmfCookie')
    //         ->will($this->returnValue($validCookie));

    //     $result = $test->getUser();
        
    //     $this->assertNotNull($result);
    // }
}
