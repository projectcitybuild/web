<?php

namespace Tests\Library\QueryPlayer;

use Tests\TestCase;
use App\Library\QueryPlayer\GameAdapters\MojangUuidAdapter;
use App\Entities\Players\Services\MinecraftPlayerLookupService;
use App\Library\Mojang\Api\MojangPlayerApi;
use App\Library\Mojang\Models\MojangPlayer;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MojangUuidAdapter_Test extends TestCase
{
    use RefreshDatabase;

    private $userLookupServiceStub;
    private $playerApiStub;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userLookupServiceStub = $this->getMockBuilder(MinecraftPlayerLookupService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->playerApiStub = $this->getMockBuilder(MojangPlayerApi::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testGetSingleIdentifier()
    {
        // given...
        $player = new MojangPlayer('test_uuid', 'test_alias');

        $this->playerApiStub
            ->expects($this->once())
            ->method('getUuidBatchOf')
            ->willReturn([$player]);

        $adapter = new MojangUuidAdapter($this->playerApiStub, $this->userLookupServiceStub);

        // when...
        $identifiers = $adapter->getUniqueIdentifiers([$player->getAlias()]);

        // expect...
        $this->assertEquals(1, count($identifiers));
        $this->assertEquals($player->getUuid(), $identifiers[0]);
    }

    public function testGetMultipleIdentifiers()
    {
        // given...
        $player1 = new MojangPlayer('test_uuid1', 'test_alias1');
        $player2 = new MojangPlayer('test_uuid2', 'test_alias2');

        $this->playerApiStub
            ->expects($this->once())
            ->method('getUuidBatchOf')
            ->willReturn([$player1, $player2]);

        $adapter = new MojangUuidAdapter($this->playerApiStub, $this->userLookupServiceStub);

        // when...
        $identifiers = $adapter->getUniqueIdentifiers([
            $player1->getAlias(),
            $player2->getAlias(),
        ]);

        // expect...
        $this->assertEquals(2, count($identifiers));
        $this->assertEquals($player1->getUuid(), $identifiers[0]);
        $this->assertEquals($player2->getUuid(), $identifiers[1]);
    }
    
    public function testCreateSinglePlayer()
    {
        // given...
        $userLookupService = resolve(MinecraftPlayerLookupService::class);
        $adapter = new MojangUuidAdapter($this->playerApiStub, $userLookupService);

        $identifier = ['test_uuid'];
        $this->assertDatabaseMissing('players_minecraft', ['uuid' => $identifier[0]]);

        // when...
        $adapter->createPlayers($identifier);

        // expect...
        $this->assertDatabaseHas('players_minecraft', ['uuid' => $identifier[0]]);
    }

    public function testCreateMultiplePlayer()
    {
        // given...
        $userLookupService = resolve(MinecraftPlayerLookupService::class);
        $adapter = new MojangUuidAdapter($this->playerApiStub, $userLookupService);

        $identifier = ['test_uuid1', 'test_uuid2'];
        $this->assertDatabaseMissing('players_minecraft', ['uuid' => $identifier[0]]);
        $this->assertDatabaseMissing('players_minecraft', ['uuid' => $identifier[1]]);

        // when...
        $adapter->createPlayers($identifier);

        // expect...
        $this->assertDatabaseHas('players_minecraft', ['uuid' => $identifier[0]]);
        $this->assertDatabaseHas('players_minecraft', ['uuid' => $identifier[1]]);
    }
}