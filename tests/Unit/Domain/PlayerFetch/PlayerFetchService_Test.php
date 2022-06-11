<?php

namespace Tests\Unit\Domain\PlayerFetch;

use Domain\PlayerFetch\Adapters\MojangUUIDFetchAdapter;
use Domain\PlayerFetch\Jobs\PlayerFetchJob;
use Domain\PlayerFetch\PlayerFetchService;
use Entities\Models\Eloquent\MinecraftPlayer;
use Entities\Models\Eloquent\MinecraftPlayerAlias;
use Entities\Models\GameType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Library\Mojang\Models\MojangPlayer;
use Queue;
use Repositories\PlayerFetchRepository;
use Tests\TestCase;

class PlayerFetchService_Test extends TestCase
{
    use RefreshDatabase;

    private function makeService(string $aliasToReturn, string $uuidToReturn): PlayerFetchService
    {
        $players = [new MojangPlayer($uuidToReturn, $aliasToReturn)];

        $adapter = $this->getMockBuilder(MojangUUIDFetchAdapter::class)
            ->disableOriginalConstructor()
            ->getMock();

        $adapter->method('fetch')
            ->willReturn($players);

        $repository = new PlayerFetchRepository();
        $adapterFactory = new PlayerFetchAdapterFactoryMock($adapter);

        return new PlayerFetchService($repository, $adapterFactory);
    }

    public function testFetchesMinecraftUUIDs()
    {
        $expectedAlias = 'alias';
        $expectedUUID = 'uuid';

        $service = $this->makeService($expectedAlias, $expectedUUID);
        $result = $service->fetchSynchronously(GameType::MINECRAFT, [$expectedAlias]);

        $this->assertEquals(1, count($result));
        $this->assertEquals($expectedAlias, $result[0]->getAlias());
        $this->assertEquals($expectedUUID, $result[0]->getUuid());
    }

    public function testCreatesPlayerThatDoesntExist()
    {
        $expectedAlias = 'alias';
        $expectedUUID = 'uuid';

        $this->assertDatabaseMissing('players_minecraft', [
            'uuid' => $expectedUUID,
        ]);
        $this->assertDatabaseMissing('players_minecraft_aliases', [
            'alias' => $expectedAlias,
        ]);

        $service = $this->makeService($expectedAlias, $expectedUUID);
        $service->fetchSynchronously(GameType::MINECRAFT, [$expectedAlias]);

        $this->assertDatabaseHas('players_minecraft', [
            'uuid' => $expectedUUID,
        ]);
        $this->assertDatabaseHas('players_minecraft_aliases', [
            'alias' => $expectedAlias,
        ]);
    }

    public function testDoesntCreatePlayerThatAlreadyExists()
    {
        $expectedAlias = 'alias';
        $expectedUUID = 'uuid';

        $player = MinecraftPlayer::create([
            'uuid' => $expectedUUID,
            'account_id' => null,
        ]);
        MinecraftPlayerAlias::create([
            'player_minecraft_id' => $player->getKey(),
            'alias' => $expectedAlias,
            'registered_at' => null,
        ]);

        $this->assertDatabaseHas('players_minecraft', [
            'uuid' => $expectedUUID,
        ]);
        $this->assertDatabaseHas('players_minecraft_aliases', [
            'alias' => $expectedAlias,
        ]);

        $service = $this->makeService($expectedAlias, $expectedUUID);
        $service->fetchSynchronously(GameType::MINECRAFT, [$expectedAlias]);

        $players = MinecraftPlayer::where('uuid', $expectedUUID)->get();
        $this->assertEquals(1, count($players));
    }

    public function testAddsAliasForPlayerThatChangedName()
    {
        $initialAlias = 'alias_1';
        $newAlias = 'alias_2';
        $expectedUUID = 'uuid';

        $player = MinecraftPlayer::create([
            'uuid' => $expectedUUID,
            'account_id' => null,
        ]);
        MinecraftPlayerAlias::create([
            'player_minecraft_id' => $player->getKey(),
            'alias' => $initialAlias,
            'registered_at' => null,
        ]);

        $this->assertDatabaseHas('players_minecraft', [
            'uuid' => $expectedUUID,
        ]);
        $this->assertDatabaseHas('players_minecraft_aliases', [
            'alias' => $initialAlias,
        ]);

        $service = $this->makeService($newAlias, $expectedUUID);
        $service->fetchSynchronously(GameType::MINECRAFT, [$newAlias]);

        $player = MinecraftPlayer::where('uuid', $expectedUUID)->first();
        $aliases = $player->aliases;

        $this->assertEquals($initialAlias, $aliases[0]->alias);
        $this->assertEquals($player->getKey(), $aliases[0]->player_minecraft_id);

        $this->assertEquals($newAlias, $aliases[1]->alias);
        $this->assertEquals($player->getKey(), $aliases[1]->player_minecraft_id);
    }

    public function testFetchDispatchesJob()
    {
        Queue::fake();

        $service = $this->makeService('alias', 'uuid');
        $service->fetch(GameType::MINECRAFT, ['alias']);

        Queue::assertPushed(PlayerFetchJob::class, 1);
    }
}
