<?php

namespace Tests\Unit\Domain\ServerStatus;

use Domain\ServerStatus\Entities\ServerQueryResult;
use Domain\ServerStatus\Events\ServerStatusFetched;
use Domain\ServerStatus\Jobs\ServerQueryJob;
use Domain\ServerStatus\Repositories\ServerStatusRepository;
use Domain\ServerStatus\ServerQueryService;
use Entities\Models\Eloquent\Server;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Queue;
use Tests\TestCase;

class ServerQueryService_Test extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Event::fake();
        Queue::fake();
    }

    public function testReturnsOnlineServerStatus()
    {
        $expectedResult = ServerQueryResult::online(1, 5, ['name']);

        $adapter = new ServerQueryAdapterMock($expectedResult);
        $adapterFactory = new ServerQueryAdapterFactoryMock($adapter);
        $service = new ServerQueryService($adapterFactory);
        $server = Server::factory()->hasCategory()->create();

        $result = $service->querySynchronously($server, new ServerStatusRepository());

        $this->assertEquals($result, $expectedResult);
    }

    public function testReturnsOfflineServerStatus()
    {
        $expectedResult = ServerQueryResult::offline();

        $adapter = new ServerQueryAdapterMock($expectedResult);
        $adapterFactory = new ServerQueryAdapterFactoryMock($adapter);
        $service = new ServerQueryService($adapterFactory);
        $server = Server::factory()->hasCategory()->create();

        $result = $service->querySynchronously($server, new ServerStatusRepository());

        $this->assertEquals($result, $expectedResult);
    }

    public function testPersistsOnlineServerStatus()
    {
        $expectedResult = ServerQueryResult::online(1, 5, ['name']);

        $adapter = new ServerQueryAdapterMock($expectedResult);
        $adapterFactory = new ServerQueryAdapterFactoryMock($adapter);
        $service = new ServerQueryService($adapterFactory);
        $server = Server::factory()->hasCategory()->create();

        $service->querySynchronously($server, new ServerStatusRepository());

        $this->assertDatabaseHas('server_statuses', [
            'server_id'      => $server->getKey(),
            'is_online'      => true,
            'num_of_players' => 1,
            'num_of_slots'   => 5,
        ]);
    }

    public function testPersistsOfflineServerStatus()
    {
        $expectedResult = ServerQueryResult::offline();

        $adapter = new ServerQueryAdapterMock($expectedResult);
        $adapterFactory = new ServerQueryAdapterFactoryMock($adapter);
        $service = new ServerQueryService($adapterFactory);
        $server = Server::factory()->hasCategory()->create();

        $service->querySynchronously($server, new ServerStatusRepository());

        $this->assertDatabaseHas('server_statuses', [
            'server_id'      => $server->getKey(),
            'is_online'      => false,
            'num_of_players' => 0,
            'num_of_slots'   => 0,
        ]);
    }

    public function testAsyncQueryDispatchesJob()
    {
        $expectedResult = ServerQueryResult::online(1, 5, ['name']);
        $adapter = new ServerQueryAdapterMock($expectedResult);
        $adapterFactory = new ServerQueryAdapterFactoryMock($adapter);
        $service = new ServerQueryService($adapterFactory);
        $server = Server::factory()->hasCategory()->create();

        $service->query($server);

        Queue::assertPushed(ServerQueryJob::class, 1);
    }

    public function testFiresEventAfterFetchingStatus()
    {
        $expectedResult = ServerQueryResult::offline();

        $adapter = new ServerQueryAdapterMock($expectedResult);
        $adapterFactory = new ServerQueryAdapterFactoryMock($adapter);
        $service = new ServerQueryService($adapterFactory);
        $server = Server::factory()->hasCategory()->create();

        $service->querySynchronously($server, new ServerStatusRepository());

        Event::assertDispatched(ServerStatusFetched::class);
    }
}
