<?php
namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Modules\Servers\Services\Querying\GameAdapters\MinecraftQueryAdapter;
use App\Modules\Servers\Services\Querying\QueryResult;
use App\Modules\Servers\Services\Mojang\UuidFetcher;
use xPaw\{MinecraftQuery, MinecraftQueryException};

class MinecraftQueryAdapter_Test extends TestCase {

    private $serverQueryMock;

    public function setUp() {
        parent::setUp();
        
        $this->serverQueryMock = $this->getMockBuilder(MinecraftQuery::class)
            ->getMock();

        $this->uuidFetcherMock = $this->getMockBuilder(UuidFetcher::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Tests that a null IP will throw an exception
     *
     * @return void
     */
    public function testQuery_whenEmptyIp_throwsException() {
        $this->expectException(\Exception::class);

        $test = new MinecraftQueryAdapter($this->serverQueryMock, $this->uuidFetcherMock);
        $response = $test->query('', '80');
    }

    /**
     * Tests that the adapter passes on both the IP and port
     * to the query service
     *
     * @return void
     */
    public function testQuery_queriesGivenAddress() {
        $this->serverQueryMock
            ->expects($this->once())
            ->method('Connect')
            ->with(
                $this->equalTo('192.168.0.1'), 
                $this->equalTo('25565')
            );

        $this->serverQueryMock
            ->method('GetInfo')
            ->will($this->returnValue([
                'Players' => 5,
                'MaxPlayers' => 10,
            ]));

        $adapter = new MinecraftQueryAdapter($this->serverQueryMock, $this->uuidFetcherMock);
        $test = $adapter->query('192.168.0.1', '25565');
    }

    /**
     * Tests that a query against an online server returns a QueryResult
     *
     * @return void
     */
    public function testQuery_whenServerOnline_returnsQueryResult() {
        $this->serverQueryMock
            ->method('GetInfo')
            ->will($this->returnValue([
                'Players' => 5,
                'MaxPlayers' => 10,
            ]));

        $adapter = new MinecraftQueryAdapter($this->serverQueryMock, $this->uuidFetcherMock);
        $test = $adapter->query('fake_ip', 'fake_port');
        $this->assertInstanceOf(QueryResult::class, $test);
    }

    /**
     * Tests that a query against an offline server will catch the exception
     * and return a QueryResult
     *
     * @return void
     */
    public function testQuery_whenServerOffline_returnsQueryResult() {
        $this->serverQueryMock
            ->method('Connect')
            ->will($this->throwException(new MinecraftQueryException));

        $adapter = new MinecraftQueryAdapter($this->serverQueryMock, $this->uuidFetcherMock);
        $test = $adapter->query('fake_ip', 'fake_port');
        $this->assertInstanceOf(QueryResult::class, $test);
    }
    

}
