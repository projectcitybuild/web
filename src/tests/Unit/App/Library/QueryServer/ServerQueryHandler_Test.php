<?php
namespace Tests\Library\QueryServer;

use Tests\TestCase;
use App\Library\QueryServer\GameAdapters\MockQueryAdapter;
use App\Library\QueryServer\ServerQueryHandler;
use App\Entities\Eloquent\Servers\Repositories\ServerStatusRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Entities\Eloquent\Servers\Models\Server;

class ServerQueryHandler_Test extends TestCase
{
    use RefreshDatabase;

    public function testQueryOnlineServer()
    {
        // given...
        $mockAdapter = (new MockQueryAdapter())
            ->setIsOnline(true)
            ->setPlayerCount(5)
            ->setMaxPlayers(10);

        $queryHandler = new ServerQueryHandler(new ServerStatusRepository);
        $queryHandler->setAdapter($mockAdapter);

        $server = factory(Server::class)->create();

        // when...
        $status = $queryHandler->queryServer($server->getKey(), '192.168.0.1', '25565');

        // expect...
        $this->assertEquals(5, $status->getNumOfPlayers());
        $this->assertEquals(10, $status->getNumOfSlots());
        $this->assertTrue($status->isOnline());
    }

    public function testQueryOfflineServer()
    {
        // given...
        $mockAdapter = (new MockQueryAdapter())
            ->setIsOnline(false);

        $queryHandler = new ServerQueryHandler(new ServerStatusRepository);
        $queryHandler->setAdapter($mockAdapter);

        $server = factory(Server::class)->create();

        // when...
        $status = $queryHandler->queryServer($server->getKey(), '192.168.0.1', '25565');

        // expect...
        $this->assertFalse($status->isOnline());
    }

    public function testQuerySavesOnlineStatus()
    {
        // given...
        $mockAdapter = (new MockQueryAdapter())
            ->setIsOnline(true)
            ->setPlayerCount(5)
            ->setMaxPlayers(10);

        $queryHandler = new ServerQueryHandler(new ServerStatusRepository);
        $queryHandler->setAdapter($mockAdapter);

        $server = factory(Server::class)->create();

        // when...
        $queryHandler->queryServer($server->getKey(), '192.168.0.1', '25565');

        // expect...
        $this->assertDatabaseHas('server_statuses', [
            'server_id'      => $server->getKey(),
            'is_online'      => true,
            'num_of_players' => 5,
            'num_of_slots'   => 10,
        ]);
    }

    public function testQuerySavesOfflineStatus()
    {
        // given...
        $mockAdapter = (new MockQueryAdapter())
            ->setIsOnline(false);

        $queryHandler = new ServerQueryHandler(new ServerStatusRepository);
        $queryHandler->setAdapter($mockAdapter);

        $server = factory(Server::class)->create();

        // when...
        $queryHandler->queryServer($server->getKey(), '192.168.0.1', '25565');

        // expect...
        $this->assertDatabaseHas('server_statuses', [
            'server_id'      => $server->getKey(),
            'is_online'      => false,
            'num_of_players' => 0,
            'num_of_slots'   => 0,
        ]);
    }

}