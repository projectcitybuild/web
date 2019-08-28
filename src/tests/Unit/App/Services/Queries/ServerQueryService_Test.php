<?php
namespace Tests\Services;

use Tests\TestCase;
use App\Services\Queries\ServerQueryService;
use Illuminate\Support\Facades\Queue;
use App\Entities\Eloquent\GameType;
use App\Services\Queries\Jobs\ServerQueryJob;
use App\Services\Queries\Jobs\PlayerQueryJob;
use App\Services\Queries\Entities\ServerJobEntity;
use App\Library\QueryServer\GameAdapters\MinecraftQueryAdapter;
use App\Library\QueryPlayer\GameAdapters\MojangUuidAdapter;
use App\Library\QueryServer\ServerQueryResult;
use App\Entities\Eloquent\Servers\Repositories\ServerStatusPlayerRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ServerQueryService_Test extends TestCase
{
    use RefreshDatabase;
    
    private function getEntityStub() : ServerJobEntity
    {
        $entity = new ServerJobEntity(
            resolve(MinecraftQueryAdapter::class),
            resolve(MojangUuidAdapter::class),
            'minecraft',
            1,
            'test_ip',
            'test_port',
            false
        );
        $entity->setServerStatusId(5);

        return $entity;
    }

    public function testDispatchesServerQueryJob()
    {
        // given...
        Queue::fake();
        $service = new ServerQueryService(new ServerStatusPlayerRepository());

        // when...
        $service->dispatchQuery(new GameType(GameType::Minecraft), 1, 'test_ip', 'test_port');
    
        // expect...
        Queue::assertPushed(ServerQueryJob::class, 1);
    }

    public function testDispatchePlayerQueryJob()
    {
        // given...
        Queue::fake();
        $service = new ServerQueryService(new ServerStatusPlayerRepository());

        // when...
        $entity = $this->getEntityStub();
        $result = new ServerQueryResult(true, 2, 5, ['player1', 'player2']);

        $service->processServerResult($entity, $result);
    
        // expect...
        Queue::assertPushed(PlayerQueryJob::class, 1);
    }

    public function testCreatesPlayerRecords()
    {
        // given...
        $expectedPlayerIds = [1, 2];
        $entity = $this->getEntityStub();
        $service = new ServerQueryService(new ServerStatusPlayerRepository());

        // when...
        $service->processPlayerResult($entity, $expectedPlayerIds);

        // expect...
        $this->assertDatabaseHas('server_statuses_players', [
            'server_status_id' => $entity->getServerStatusId(),
            'player_type' => $entity->getGameIdentifier(),
            'player_id' => $expectedPlayerIds[0],
        ]);
        $this->assertDatabaseHas('server_statuses_players', [
            'server_status_id' => $entity->getServerStatusId(),
            'player_type' => $entity->getGameIdentifier(),
            'player_id' => $expectedPlayerIds[1],
        ]);
    }
}
