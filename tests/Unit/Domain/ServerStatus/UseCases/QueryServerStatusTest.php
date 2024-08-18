<?php

namespace Tests\Unit\Domain\ServerStatus\UseCases;

use App\Domains\ServerStatus\Adapters\ServerQueryAdapter;
use App\Domains\ServerStatus\Adapters\ServerQueryAdapterFactory;
use App\Domains\ServerStatus\Entities\ServerQueryResult;
use App\Domains\ServerStatus\UseCases\QueryServerStatus;
use App\Models\Server;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Repositories\ServerRepository;
use Tests\TestCase;

class QueryServerStatusTest extends TestCase
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
        $server = Server::factory()->create();

        $expectedResult = ServerQueryResult::online(
            numOfPlayers: 1,
            numOfSlots: 5,
            onlinePlayerNames: ['name'],
        );

        $this->serverQueryAdapter
            ->expects('query')
            ->andReturns($expectedResult);

        $service = new QueryServerStatus(
            queryAdapterFactory: $this->serverQueryAdapterFactory,
            serverRepository: new ServerRepository(),
        );

        $result = $service->query(server: $server);

        $this->assertEquals($result, $expectedResult);
        $this->assertDatabaseHas('servers', [
            'server_id' => $server->getKey(),
            'is_online' => true,
            'num_of_players' => 1,
            'num_of_slots' => 5,
        ]);
    }

    public function test_persists_offline_server_status()
    {
        $server = Server::factory()->create();
        $expectedResult = ServerQueryResult::offline();

        $this->serverQueryAdapter
            ->expects('query')
            ->andReturns($expectedResult);

        $service = new QueryServerStatus(
            queryAdapterFactory: $this->serverQueryAdapterFactory,
            serverRepository: new ServerRepository(),
        );

        $result = $service->query(server: $server);

        $this->assertEquals($result, $expectedResult);
        $this->assertDatabaseHas('servers', [
            'server_id' => $server->getKey(),
            'is_online' => false,
            'num_of_players' => 0,
            'num_of_slots' => 0,
        ]);
    }
}
