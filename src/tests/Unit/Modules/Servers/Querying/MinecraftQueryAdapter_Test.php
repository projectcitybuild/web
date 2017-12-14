<?php
namespace Tests\Unit;

use Tests\TestCase;
use App\Modules\Servers\Services\Querying\GameAdapters\MinecraftQueryAdapter;
use xPaw\MinecraftQuery;
use App\Modules\Servers\Services\Querying\QueryResult;
use xPaw\MinecraftQueryException;

class MinecraftQueryAdapter_Test extends TestCase {

    private $mockQueryService;

    protected function setUp() {
        $this->mockQueryService = $this->getMockBuilder(MinecraftQuery::class)->getMock();
    }

    /**
     * Tests that giving an empty string as an ip will throw an exception
     *
     * @return void
     */
    public function testQuery_whenBadIp_throwsException() {
        $this->expectException(\Exception::class);

        $test = new MinecraftQueryAdapter($this->mockQueryService);
        $test->query('', 25565);
    }

    /**
     * Tests that the server query service is called
     *
     * @return void
     */
    public function testQuery_callsQueryService() {
        $this->mockQueryService
            ->expects($this->once())
            ->method('Connect');

        $test = new MinecraftQueryAdapter($this->mockQueryService);
        $test->query('198.168.0.1', 25565);
    }

    /**
     * Tests that the server's IP and port are passed to the query service
     *
     * @return void
     */
    public function testQuery_passesIpAndPortToService() {
        $this->mockQueryService
            ->expects($this->once())
            ->method('Connect')
            ->with(
                $this->equalTo('198.168.0.1'),
                $this->equalTo(25565)
            );

        $test = new MinecraftQueryAdapter($this->mockQueryService);
        $test->query('198.168.0.1', 25565);
    }
    
    /**
     * Tests that query of an online server returns a QueryResult instance
     *
     * @return void
     */
    public function testQuery_whenOnline_returnsQueryResult() {
        $this->mockQueryService
            ->method('Connect');

        $test = new MinecraftQueryAdapter($this->mockQueryService);
        $result = $test->query('198.168.0.1', 25565);

        $this->assertInstanceOf(QueryResult::class, $result);
    }

    /**
     * Tests that query of an offline server returns a QueryResult instance
     *
     * @return void
     */
    public function testQuery_whenOffline_returnsQueryResult() {
        $this->mockQueryService
            ->method('Connect')
            ->will($this->throwException(new MinecraftQueryException));

        $test = new MinecraftQueryAdapter($this->mockQueryService);
        $result = $test->query('198.168.0.1', 25565);

        $this->assertInstanceOf(QueryResult::class, $result);
    }

}
