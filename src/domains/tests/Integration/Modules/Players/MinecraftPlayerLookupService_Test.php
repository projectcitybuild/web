<?php
namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use App\Modules\Servers\Services\Querying\ServerQueryService;
use App\Modules\Servers\Services\Querying\GameAdapters\MockQueryAdapter;
use App\Modules\Servers\Models\ServerStatus;
use App\Modules\Servers\Models\Server;
use App\Modules\Players\Services\MinecraftPlayerLookupService;
use App\Modules\Players\Models\MinecraftPlayer;
use App\Modules\Players\Models\MinecraftPlayerAlias;

class MinecraftPlayerLookupService_Test extends TestCase
{
    use RefreshDatabase;

    /**
     * When a player with the given uuid exists, that player
     * should be returned
     *
     * @return void
     */
    public function testGetByUuid_whenExists_returnsMinecraftPlayer()
    {
        $player = factory(MinecraftPlayer::class)->create([
            'uuid' => 'test_uuid',
        ]);

        $test = resolve(MinecraftPlayerLookupService::class);
        $result = $test->getByUuid('test_uuid');

        $this->assertEquals($player->player_minecraft_id, $result->player_minecraft_id);
    }

    /**
     * When a player with the given uuid does not exist, nothing
     * should be returned
     *
     * @return void
     */
    public function testGetByUuid_whenNotExists_returnsNull()
    {
        $test = resolve(MinecraftPlayerLookupService::class);
        $result = $test->getByUuid('test_uuid');

        $this->assertNull($result);
    }

    /**
     * When a player with the given alias exists, that player
     * should be returned
     *
     * @return void
     */
    public function testGetByAlias_whenExists_returnsMinecraftPlayer()
    {
        $player = factory(MinecraftPlayer::class)->create();
        $alias = factory(MinecraftPlayerAlias::class)->create([
            'alias' => 'test_alias',
            'player_minecraft_id' => $player->player_minecraft_id,
        ]);

        $test = resolve(MinecraftPlayerLookupService::class);
        $result = $test->getByAlias('test_alias');

        $this->assertEquals($player->player_minecraft_id, $result->player_minecraft_id);
    }

    /**
     * Using getOrCreateByUuid(), when the given uuid exists
     * that player should be returned
     *
     * @return void
     */
    public function testGetOrCreate_whenExists_returnsMinecraftPlayer()
    {
        $player = factory(MinecraftPlayer::class)->create([
            'uuid' => 'test_uuid',
        ]);

        $test = resolve(MinecraftPlayerLookupService::class);
        $result = $test->getOrCreateByUuid('test_uuid');

        $this->assertEquals($player->player_minecraft_id, $result->player_minecraft_id);
    }

    /**
     * Using getOrCreateByUuid(), when the given uuid does not
     * exist, a new player should be created
     *
     * @return void
     */
    public function testGetOrCreate_whenNotExists_createsMinecraftPlayer()
    {
        $test = resolve(MinecraftPlayerLookupService::class);
        $result = $test->getOrCreateByUuid('new_uuid');

        $this->assertDatabaseHas('players_minecraft', [
            'uuid' => 'new_uuid',
        ]);
    }

    /**
     * Using getOrCreateByUuid(), when the given uuid does not
     * exist, a new player should be created and additionally the
     * given alias should be assigned to it
     *
     * @return void
     */
    public function testGetOrCreate_whenNotExists_andAliasGiven_createsWithAlias()
    {
        $test = resolve(MinecraftPlayerLookupService::class);
        $result = $test->getOrCreateByUuid('new_uuid', 'new_alias');

        $player = MinecraftPlayer::where('uuid', 'new_uuid')->first();
        
        $this->assertDatabaseHas('players_minecraft_aliases', [
            'player_minecraft_id' => $player->player_minecraft_id,
            'alias' => 'new_alias',
        ]);
    }
}
