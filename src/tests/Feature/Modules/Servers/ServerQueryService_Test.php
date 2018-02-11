<?php
namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use App\Modules\Servers\Services\Querying\ServerQueryService;
use App\Modules\Servers\Services\Querying\GameAdapters\MockQueryAdapter;
use App\Modules\Servers\Models\ServerStatus;
use App\Modules\Servers\Models\Server;

class ServerQueryService_Test extends TestCase {
    use RefreshDatabase;

    // /**
    //  * Tests that querying an offline server generates an expected ServerStatus model
    //  *
    //  * @return void
    //  */
    // public function testQueryServer_whenServerOffline_createsModel() {
    //     $test = resolve(ServerQueryService::class);
    //     $test->queryServer(
    //         new MockQueryAdapter(false),
    //         5,
    //         '192.168.0.1',
    //         '25565'
    //     );

    //     $this->assertDatabaseHas(ServerStatus::getTableName(), [
    //         'server_id' => 5,
    //         'is_online' => false,
    //         'num_of_players' => 0,
    //         'num_of_slots' => 0,
    //     ]);
    // }

    // /**
    //  * Tests that querying an online server generates an expected ServerStatus model
    //  *
    //  * @return void
    //  */
    // public function testQueryServer_whenServerOnline_createsModel() {
    //     $test = resolve(ServerQueryService::class);
    //     $test->queryServer(
    //         new MockQueryAdapter(true, 2, 80, ['_andy', '_specialk']),
    //         6,
    //         '192.168.0.1',
    //         '25565'
    //     );

    //     $this->assertDatabaseHas(ServerStatus::getTableName(), [
    //         'server_id' => 6,
    //         'is_online' => true,
    //         'num_of_players' => 2,
    //         'num_of_slots' => 80,
    //         'players' => '_andy,_specialk',
    //     ]);
    // }

    // /**
    //  * Tests that queriable servers are all queried
    //  *
    //  * @return void
    //  */
    // public function testQueryAllServers_usesAllQueriableServers() {
    //     $server1 = factory(Server::class)->create(['is_querying' => true]);
    //     $server2 = factory(Server::class)->create(['is_querying' => true]);
    //     $server3 = factory(Server::class)->create(['is_querying' => false]);

    //     $test = resolve(ServerQueryService::class);
    //     $test->queryAllServers(new MockQueryAdapter(false));

    //     $this->assertDatabaseHas(ServerStatus::getTableName(), [
    //         'server_id' => $server1->server_id,
    //         'is_online' => false,
    //     ]);
    //     $this->assertDatabaseHas(ServerStatus::getTableName(), [
    //         'server_id' => $server2->server_id,
    //         'is_online' => false,
    //     ]);
    // }

}
