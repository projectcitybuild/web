<?php
namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Modules\Servers\Services\Querying\Jobs\CreateMinecraftPlayerJob;
use App\Modules\Servers\Services\Mojang\UuidFetcher;
use App\Modules\Servers\Services\Mojang\MojangPlayer;
use App\Modules\Users\Services\GameUserLookupService;

class CreateMinecraftPlayerJob_Test extends TestCase {

    private $gameUserLookupMock;
    private $uuidFetchMock;
    private $uuidResultMock;

    public function setUp() {
        parent::setUp();

        $this->gameUserLookupMock = $this->getMockBuilder(GameUserLookupService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->uuidFetchMock = $this->getMockBuilder(UuidFetcher::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->uuidResultMock = $this->getMockBuilder(MojangPlayer::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Tests that when the uuid lookup service hands over a uuid,
     * the job passes it onto the PCB game user creation service
     *
     * @return void
     */
    public function testHandle_whenUuidReceived_willLookupGameUser() {
        $this->uuidResultMock
            ->method('getUuid')
            ->will($this->returnValue('123456'));

        $this->uuidFetchMock
            ->expects($this->once())
            ->method('getUuidOf')
            ->will($this->returnValue($this->uuidResultMock));

        $job = new CreateMinecraftPlayerJob('test_player', 0);
        $test = $job->handle($this->uuidFetchMock, $this->gameUserLookupMock);
    }

    /**
     * Tests that when the uuid lookup service fails to find a uuid,
     * it throws an exception so that the job fails and we can retry it later
     *
     * @return void
     */
    public function testHandle_whenUuidFetchFails_throwsException() {
        $this->expectException(\Exception::class);

        $this->uuidResultMock
            ->method('getUuid')
            ->will($this->returnValue(null));

        $this->uuidFetchMock
            ->expects($this->once())
            ->method('getUuidOf')
            ->will($this->returnValue($this->uuidResultMock));

        $job = new CreateMinecraftPlayerJob('test_player', 0);
        $test = $job->handle($this->uuidFetchMock, $this->gameUserLookupMock);
    }
    

}
