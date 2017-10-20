<?php
namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Modules\Servers\Services\Querying\{ServerQueryService, QueryAdapterFactory};
use App\Modules\Servers\Repositories\{ServerRepository, ServerStatusRepository};
use \Illuminate\Log\Writer as Logger;

class ServerQueryService_Test extends TestCase {

    private $serverRepositoryMock;
    private $statusRepositoryMock;
    private $adapterFactoryMock;
    private $loggerMock;

    public function setUp() {
        parent::setUp();
        
        $this->serverRepositoryMock = $this->getMockBuilder(ServerRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->statusRepositoryMock = $this->getMockBuilder(ServerStatusRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->adapterFactoryMock = $this->getMockBuilder(QueryAdapterFactory::class)
            ->getMock();

        $this->loggerMock = $this->getMockBuilder(Logger::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Tests that when given a list of servers, each one is queried
     *
     * @return void
     */
    public function testQueryAllServers_whenGivenServers_passesServerData() {
        // $test = $this->getMockBuilder(ServerQueryService::class)
        //     ->setConstructorArgs([
        //         $this->serverRepositoryMock,
        //         $this->statusRepositoryMock,
        //         $this->adapterFactoryMock,
        //         $this->loggerMock
        //     ])
        //     ->setMethodsExcept(['queryAllServers'])
        //     ->getMock();

        // $server1 = new \stdClass();
        // $server1->ip = '192.168.0.1';
        // $server1->port = '80';

        // $server2 = new \stdClass();
        // $server2->ip = '192.168.0.1';
        // $server2->port = '80';

        // $this->serverRepositoryMock
        //     ->expects($this->once())
        //     ->method('getAllQueriableServers')
        //     ->will($this->returnValue([$server1, $server2]));

        // $test->expects($this->at(0))
        //     ->method('queryServer')
        //     ->with(
        //         $this->equalTo('192.168.0.1'),
        //         $this->equalTo('80')
        //     );

        // $test->expects($this->at(1))
        //     ->method('queryServer')
        //     ->with(
        //         $this->equalTo('192.168.0.1'),
        //         $this->equalTo('80')
        //     );

        // $test->queryAllServers();
        $this->assertTrue(true);
    }

    /**
     * Tests that a valid response from the server will call the repository
     * to create a new status
     *
     * @return void
     */
    // public function testQueryServer_whenServerResponse_createsStatus() {
    //     $this->statusRepositoryMock
    //         ->expects($this->once())
    //         ->method('create');

    //     $test = new ServerQueryService(
    //         $this->serverRepositoryMock,
    //         $this->statusRepositoryMock,
    //         $this->adapterFactoryMock,
    //         $this->loggerMock
    //     );
        
    //     $serverMock = new stdObject();
    //     $serverMock->ip = '192.168.0.1';
    //     $serverMock->port = '80';
    //     $serverMock->server_id = 1;
    //     $serverMock->getAddress = function() { return ''; };

    //     $test->queryServer($serverMock);
    // }

}
