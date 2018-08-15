<?php
namespace Tests\Library\QueryServer;

use Tests\TestCase;
use Domains\Library\QueryServer\GameAdapters\MockQueryAdapter;
use Domains\Library\QueryServer\ServerQueryHandler;
use Illuminate\Log\Logger;
use Domains\Modules\Servers\Repositories\ServerStatusRepository;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ServerQueryHandler_Test extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;

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

        $queryHandler = new ServerQueryHandler(new ServerStatusRepository, $this->logStub);
        $queryHandler->setAdapter($mockAdapter);

        // when...
        $status = $queryHandler->queryServer(1, '192.168.0.1', '25565');

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

        $queryHandler = new ServerQueryHandler(new ServerStatusRepository, $this->logStub);
        $queryHandler->setAdapter($mockAdapter);

        // when...
        $status = $queryHandler->queryServer(1, '192.168.0.1', '25565');

        // expect...
        $this->assertFalse($status->isOnline());
    }

    public function testQuerySavesOnlineStatus()
    {
        // given...
        $mockAdapter = new MockQueryAdapter();
        $mockAdapter
            ->setIsOnline(true)
            ->setPlayerCount(5)
            ->setMaxPlayers(10);

        $queryHandler = new ServerQueryHandler(new ServerStatusRepository, $this->logStub);
        $queryHandler->setAdapter($mockAdapter);

        // when...
        $status = $queryHandler->queryServer(1, '192.168.0.1', '25565');

        // expect...
        $this->assertDatabaseHas('server_statuses', [
            'server_id'      => 1,
            'is_online'      => true,
            'num_of_players' => 5,
            'num_of_slots'   => 10,
        ]);
    }

    public function testQuerySavesOfflineStatus()
    {
        // given...
        $mockAdapter = new MockQueryAdapter();
        $mockAdapter
            ->setIsOnline(false);

        $queryHandler = new ServerQueryHandler(new ServerStatusRepository, $this->logStub);
        $queryHandler->setAdapter($mockAdapter);

        // when...
        $status = $queryHandler->queryServer(1, '192.168.0.1', '25565');

        // expect...
        $this->assertDatabaseHas('server_statuses', [
            'server_id'      => 1,
            'is_online'      => false,
            'num_of_players' => 0,
            'num_of_slots'   => 0,
        ]);
    }
}