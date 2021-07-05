<?php

namespace Tests\Feature;

use App\Entities\Accounts\Models\Account;
use App\Entities\Groups\Models\Group;
use App\Entities\Players\Models\MinecraftPlayer;
use App\Library\Mojang\Api\MojangPlayerApi;
use App\Library\Mojang\Models\MojangPlayer;
use App\Library\Mojang\Models\MojangPlayerNameHistory;
use Mockery\MockInterface;
use Tests\TestCase;

class PanelMinecraftPlayerReloadAliasTest extends TestCase
{
    private $adminAccount;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminAccount = Account::factory()->create();

        $adminGroup = Group::create([
            'name' => 'Administrator',
            'can_access_panel' => true,
        ]);

        $this->adminAccount->groups()->attach($adminGroup->group_id);

        $this->withoutExceptionHandling();
    }

    public function testReloadAlias()
    {
        $minecraftPlayer = MinecraftPlayer::factory()->hasAliases(1)->create();

        $this->mock(MojangPlayerApi::class, function (MockInterface $mock) {
            $mock->shouldReceive('getNameHistoryOf')->once()->andReturn(
                new MojangPlayerNameHistory([
                    (object)['name' => 'Original'],
                    (object)['name' => 'Second', 'changeToAt' => 1423063907000],
                    (object)['name' => 'Third', 'changeToAt' => 1425714026000]
                ])
            );
        });

        $this->actingAs($this->adminAccount)
            ->post(route('front.panel.minecraft-players.reload-alias', $minecraftPlayer));

        $this->assertDatabaseHas('players_minecraft_aliases', [
            'player_minecraft_id' => $minecraftPlayer->player_minecraft_id,
            'alias' => 'Third',
        ]);
    }

    public function testReloadWithNoAliasChange()
    {
        $minecraftPlayer = MinecraftPlayer::factory()->hasAliases(1, ['alias' => 'MyAlias'])->create();

        $this->mock(MojangPlayerApi::class, function (MockInterface $mock) {
            $mock->shouldReceive('getNameHistoryOf')->once()->andReturn(
                new MojangPlayerNameHistory([
                    (object)['name' => 'MyAlias'],
                ])
            );
        });

        $this->actingAs($this->adminAccount)
            ->post(route('front.panel.minecraft-players.reload-alias', $minecraftPlayer));

        $this->assertDatabaseCount('players_minecraft_aliases', 1);
    }
}
