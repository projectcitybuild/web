<?php

namespace Tests\Library\QueryServer;

use App\Entities\Players\Services\MinecraftPlayerLookupService;
use App\Library\Mojang\Api\MojangPlayerApi;
use App\Library\Mojang\Models\MojangPlayer;
use App\Library\QueryPlayer\GameAdapters\MojangUuidAdapter;
use App\Library\QueryPlayer\PlayerQueryHandler;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlayerQueryHandler_Test extends TestCase
{
    use RefreshDatabase;

    private $playerApiMock;

    protected function setUp(): void
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
