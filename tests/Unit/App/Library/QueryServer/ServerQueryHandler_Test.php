<?php
namespace Tests\Library\QueryServer;

use App\Entities\Servers\Models\ServerCategory;
use Tests\TestCase;
use App\Library\QueryServer\GameAdapters\MockQueryAdapter;
use App\Library\QueryServer\ServerQueryHandler;
use App\Entities\Servers\Repositories\ServerStatusRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Entities\Servers\Models\Server;

class ServerQueryHandler_Test extends TestCase
{
    use RefreshDatabase;

    public function testQueryOnlineServer()
    {
        $mockAdapter = (new MockQueryAdapter())
            ->setIsOnline(true)
            ->setPlayerCount(5)
            ->setMaxPlayers(10);

        $queryHandler = new ServerQueryHandler(new ServerStatusRepository);
        $queryHandler->setAdapter($mockAdapter);

        $server = Server::factory()
            ->has(ServerCategory::factory())
            ->create();

        $status = $queryHandler->queryServer($server->getKey(), '192.168.0.1', '25565');

        $this->assertEquals(5, $status->getNumOfPlayers());
        $this->assertEquals(10, $status->getNumOfSlots());
        $this->assertTrue($status->isOnline());
    }

    public function testQueryOfflineServer()
    {
        $mockAdapter = (new MockQueryAdapter())
            ->setIsOnline(false);

        $queryHandler = new ServerQueryHandler(new ServerStatusRepository);
        $queryHandler->setAdapter($mockAdapter);

        $server = Server::factory()
            ->has(ServerCategory::factory())
            ->create();

        $status = $queryHandler->queryServer($server->getKey(), '192.168.0.1', '25565');

        $this->assertFalse($status->isOnline());
    }

    public function testQuerySavesOnlineStatus()
    {
        $mockAdapter = (new MockQueryAdapter())
            ->setIsOnline(true)
            ->setPlayerCount(5)
            ->setMaxPlayers(10);

        $queryHandler = new ServerQueryHandler(new ServerStatusRepository);
        $queryHandler->setAdapter($mockAdapter);

        $server = Server::factory()
            ->has(ServerCategory::factory())
            ->create();

        $queryHandler->queryServer($server->getKey(), '192.168.0.1', '25565');

        $this->assertDatabaseHas('server_statuses', [
            'server_id'      => $server->getKey(),
            'is_online'      => true,
            'num_of_players' => 5,
            'num_of_slots'   => 10,
        ]);
    }

    public function testQuerySavesOfflineStatus()
    {
        $mockAdapter = (new MockQueryAdapter())
            ->setIsOnline(false);

        $queryHandler = new ServerQueryHandler(new ServerStatusRepository);
        $queryHandler->setAdapter($mockAdapter);

        $server = Server::factory()->for(ServerCategory::factory())->create();

        $queryHandler->queryServer($server->getKey(), '192.168.0.1', '25565');

        $this->assertDatabaseHas('server_statuses', [
            'server_id'      => $server->getKey(),
            'is_online'      => false,
            'num_of_players' => 0,
            'num_of_slots'   => 0,
        ]);
    }

}
