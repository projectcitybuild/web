<?php
namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Modules\Servers\Services\Querying\GameAdapters\MinecraftQueryAdapter;
use App\Modules\Servers\Services\Querying\QueryResult;
use xPaw\{MinecraftQuery, MinecraftQueryException};

class MinecraftQueryAdapterTest extends TestCase {

    private $serviceMock;

    public function setUp() {
        $this->serviceMock = $this->getMockBuilder(MinecraftQuery::class)
            ->getMock();
    }

    /**
     * Tests that the given port is used in the connection
     *
     * @return void
     */
    public function testQuery_whenPortGiven_usesPortInConnect() {
        $this->serviceMock
            ->expects($this->once())
            ->method('Connect')
            ->with($this->equalTo('192.168.0.1'), $this->equalTo('80'));
        
        $this->serviceMock
            ->expects($this->once())
            ->method('GetInfo')
            ->will($this->returnValue([
                'Players' => 5,
                'MaxPlayers' => 10,
            ]));

        $this->serviceMock
            ->expects($this->once())
            ->method('GetPlayers')
            ->will($this->returnValue([]));

        $test = new MinecraftQueryAdapter($this->serviceMock);
        $response = $test->query('192.168.0.1', '80');

        $this->assertNotNull($response);
    }
    
    /**
     * Tests that a null IP will throw an exception
     *
     * @return void
     */
    public function testQuery_whenEmptyIp_throwsException() {
        $this->expectException(\Exception::class);

        $test = new MinecraftQueryAdapter($this->serviceMock);
        $response = $test->query('', '80');
    }

    /**
     * Tests that a valid connection will return a QueryResult
     *
     * @return void
     */
    public function testQuery_whenConnectSuccess_returnsQueryResult() {
        $this->serviceMock
            ->expects($this->once())
            ->method('GetInfo')
            ->will($this->returnValue([
                'Players' => 5,
                'MaxPlayers' => 10,
            ]));

        $this->serviceMock
            ->expects($this->once())
            ->method('GetPlayers')
            ->will($this->returnValue([]));
        
        $test = new MinecraftQueryAdapter($this->serviceMock);
        $response = $test->query('192.168.0.1', '80');

        $this->assertInstanceOf(QueryResult::class, $response);
    }

    /**
     * Tests that a failed connection will still return a QueryResult
     *
     * @return void
     */
    public function testQuery_whenConnectFailed_returnsQueryResult() {
        $this->serviceMock
            ->expects($this->once())
            ->method('Connect')
            ->will($this->throwException(new MinecraftQueryException));
        
        $test = new MinecraftQueryAdapter($this->serviceMock);
        $response = $test->query('192.168.0.1', '80');

        $this->assertInstanceOf(QueryResult::class, $response);
    }

}
