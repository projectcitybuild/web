<?php
namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Modules\Forums\Services\SMF\{SmfUserFactory, SmfUser};
use App\Modules\Forums\Models\ForumUser;
use App\Modules\Forums\Repositories\ForumUserRepository;

class SmfUserFactoryTest extends TestCase {

    private $mockRepository;

    public function setUp() {
        $mockModel = $this->getMockBuilder(ForumUser::class)
            ->getMock();

        $this->mockRepository = $this->getMockBuilder(ForumUserRepository::class)
            ->setConstructorArgs([$mockModel])
            ->getMock();
    }

    /**
     * Tests that getInstance() instantiates a new SmfUser class
     *
     * @return void
     */
    public function testGetInstance_returnsSmfUser() {
        $test = new SmfUserFactory($this->mockRepository, []);
        $this->assertInstanceOf(SmfUser::class, $test->getInstance(-1));
    }

}
