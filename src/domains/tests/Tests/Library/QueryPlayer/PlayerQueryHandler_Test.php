<?php
namespace Tests\Library\QueryServer;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Domains\Library\QueryPlayer\GameAdapters\MojangUuidAdapter;
use Domains\Library\QueryPlayer\PlayerQueryHandler;
use Domains\Modules\Players\Services\MinecraftPlayerLookupService;
use Domains\Library\Mojang\Api\MojangPlayerApi;
use Domains\Library\Mojang\Models\MojangPlayer;

class PlayerQueryHandler_Test extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;

    private $playerApiMock;

    public function setUp()
    {
        parent::setUp();

        $this->playerApiMock = $this->getMockBuilder(MojangPlayerApi::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testQueryCreatesPlayer()
    {
        // given...
        $player = new MojangPlayer('test_uuid', 'test_alias');

        $this->playerApiMock
            ->expects($this->once())
            ->method('getUuidBatchOf')
            ->willReturn([$player]);

        $userLookupService = resolve(MinecraftPlayerLookupService::class);
        $mojangAdapter = new MojangUuidAdapter($this->playerApiMock, $userLookupService);

        $playerQueryHandler = new PlayerQueryHandler();
        $playerQueryHandler->setAdapter($mojangAdapter);

        $this->assertDatabaseMissing('players_minecraft', ['uuid' => $player->getUuid()]);

        // when...
        $playerQueryHandler->query([
            $player->getAlias(),
        ]);

        // expect...
        $this->assertDatabaseHas('players_minecraft', ['uuid' => $player->getUuid()]);
    }
}