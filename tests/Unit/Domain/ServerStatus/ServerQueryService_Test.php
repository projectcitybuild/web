<?php

namespace Tests\Unit\Domain\ServerStatus;

use Domain\ServerStatus\Entities\ServerQueryResult;
use Domain\ServerStatus\ServerQueryAdapter;
use Domain\ServerStatus\ServerQueryAdapterFactory;
use Domain\ServerStatus\ServerQueryService;
use Entities\Models\Eloquent\Server;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Repositories\ServerStatusRepository;
use Tests\TestCase;

class ServerQueryService_Test extends TestCase
{
    use RefreshDatabase;

    private ServerQueryAdapter $serverQueryAdapter;
    private ServerQueryAdapterFactory $serverQueryAdapterFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->serverQueryAdapter = \Mockery::mock(ServerQueryAdapter::class);
        $this->serverQueryAdapterFactory = \Mockery::mock(ServerQueryAdapterFactory::class);

        $this->serverQueryAdapterFactory
            ->expects('make')
            ->andReturn($this->serverQueryAdapter);

        Event::fake();
        Queue::fake();
    }

    public function test_persists_online_server_status()
    {
        $server = Server::factory()->hasCategory()->create();

        $expectedResult = ServerQueryResult::online(
            numOfPlayers: 1,
            numOfSlots: 5,
            onlinePlayerNames: ['name'],
        );

        $this->serverQueryAdapter
            ->expects('query')
            ->andReturns($expectedResult);

        $service = new ServerQueryService(
            queryAdapterFactory: $this->serverQueryAdapterFactory,
            serverStatusRepository: new ServerStatusRepository(),
        );

        $result = $service->query(server: $server);

        $this->assertEquals($result, $expectedResult);
        $this->assertDatabaseHas('servers', [
            'server_id'      => $server->getKey(),
            'is_online'      => true,
            'num_of_players' => 1,
            'num_of_slots'   => 5,
        ]);
    }

    public function test_persists_offline_server_status()
    {
        $server = Server::factory()->hasCategory()->create();
        $expectedResult = ServerQueryResult::offline();

        $this->serverQueryAdapter
            ->expects('query')
            ->andReturns($expectedResult);

        $service = new ServerQueryService(
            queryAdapterFactory: $this->serverQueryAdapterFactory,
            serverStatusRepository: new ServerStatusRepository(),
        );

        $result = $service->query(server: $server);

        $this->assertEquals($result, $expectedResult);
        $this->assertDatabaseHas('servers', [
            'server_id'      => $server->getKey(),
            'is_online'      => false,
            'num_of_players' => 0,
            'num_of_slots'   => 0,
        ]);
    }
}
