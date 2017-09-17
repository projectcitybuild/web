<?php
namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Modules\Forums\Services\SMF\{SmfUserFactory, SmfUser};
use App\Modules\Forums\Models\ForumUser;
use App\Modules\Forums\Repositories\ForumUserRepository;

class SmfUserTest extends TestCase {

    private $modelMock;
    private $repositoryMock;

    public function setUp() {
        $this->modelMock = $this->getMockBuilder(ForumUser::class)
            ->getMock();

        $this->repositoryMock = $this->getMockBuilder(ForumUserRepository::class)
            ->setConstructorArgs([$this->modelMock])
            ->getMock();
    }

    /**
     * Tests that when assigned a guest id, isGuest() returns true
     *
     * @return void
     */
    public function testIsGuest_whenGuestId_returnsTrue() {
        $test = new SmfUser(-1, $this->repositoryMock, []);
        $this->assertTrue($test->isGuest());
    }

    /**
     * Tests that when assigned a user id, isGuest() returns false
     *
     * @return void
     */
    public function testIsGuest_whenUserId_returnsFalse() {
        $test = new SmfUser(42, $this->repositoryMock, []);
        $this->assertFalse($test->isGuest());
    }

    /**
     * Tests that getId() returns the value assigned during construction
     *
     * @return void
     */
    public function testGetId_returnsValidId() {
        $test = new SmfUser(42, $this->repositoryMock, []);
        $this->assertEquals(42, $test->getId());
    }

    /**
     * Tests that getUserFromDatabase() returns a value from the repository
     *
     * @return void
     */
    // public function testGetUserFromDatabase_callsRepository() {
    //     $this->repositoryMock
    //         ->expects($this->once())
    //         ->method('getUserById')
    //         ->will($this->returnValue($this->modelMock));
        
    //     $test = new SmfUser(42, $this->repositoryMock, []);
    //     $this->assertInstanceOf(ForumUser::class, $test->getUserFromDatabase());
    // }

    // public function testGetUserFromDatabase_whenGuest_returnsNull() {
    //     $test = new SmfUser(-1, $this->repositoryMock, []);
    //     $this->assertNull($test->getUserFromDatabase());
    // }

}
