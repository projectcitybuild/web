<?php
namespace Tests\Library\QueryServer;

use Tests\TestCase;
use Domains\Library\QueryServer\GameAdapters\MockQueryAdapter;
use Domains\Library\QueryServer\ServerQueryHandler;
use Illuminate\Log\Logger;

class ServerQueryHandler_Test extends TestCase
{
    private $logStub;

    public function setUp() 
    {
        parent::setUp();

        $this->logStub = $this->getMockBuilder(Logger::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testQueryOnlineServer()
    {
        // given...
        $mockAdapter = new MockQueryAdapter();
        $mockAdapter
            ->setIsOnline(true)
            ->setPlayerCount(5)
            ->setMaxPlayers(10);

        $queryHandler = new ServerQueryHandler($this->logStub);
        $queryHandler->setAdapter($mockAdapter);

        // when...
        $status = $queryHandler->queryServer('192.168.0.1', '25565');

        // expect...
        $this->assertEquals(5, $status->getNumOfPlayers());
        $this->assertEquals(10, $status->getNumOfSlots());
        $this->assertTrue($status->isOnline());
    }

    public function testQueryOfflineServer()
    {
        // given...
        $mockAdapter = new MockQueryAdapter();
        $mockAdapter
            ->setIsOnline(false);

        $queryHandler = new ServerQueryHandler($this->logStub);
        $queryHandler->setAdapter($mockAdapter);

        // when...
        $status = $queryHandler->queryServer('192.168.0.1', '25565');

        // expect...
        $this->assertFalse($status->isOnline());
    }
}