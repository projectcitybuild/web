<?php

namespace Tests\Feature;

use App\Entities\Accounts\Models\Account;
use App\Entities\Groups\Models\Group;
use App\Entities\Players\Models\MinecraftPlayer;
use App\Library\Mojang\Api\MojangPlayerApi;
use App\Library\Mojang\Models\MojangPlayer;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery\MockInterface;
use Tests\TestCase;

class PanelMinecraftPlayerLookupTest extends TestCase
{
    use WithFaker;
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

    public function testLookupPlayerByUnDashedUUID()
    {
        $mcPlayer = MinecraftPlayer::factory()->create();
        $this->actingAs($this->adminAccount)
            ->post(route('front.panel.minecraft-players.lookup'), [
                'query' => $mcPlayer->uuid,
            ])
            ->assertRedirect(route('front.panel.minecraft-players.show', $mcPlayer));
    }

    public function testLookupPlayerByDashedUUID()
    {
        $uuid = $this->faker->uuid;
        $mcPlayer = MinecraftPlayer::factory()->hasAliases(1)->create([
            'uuid' => str_replace('-', '', $uuid),
        ]);

        $this->actingAs($this->adminAccount)
            ->post(route('front.panel.minecraft-players.lookup'), [
                'query' => $uuid,
            ])
            ->assertRedirect(route('front.panel.minecraft-players.show', $mcPlayer));
    }

    public function testLookupPlayerByStoredAlias()
    {
        $mcPlayer = MinecraftPlayer::factory()->hasAliases(1)->create();
        $alias = $mcPlayer->aliases()->latest()->first();

        $this->actingAs($this->adminAccount)
            ->post(route('front.panel.minecraft-players.lookup'), [
                'query' => $alias->alias,
            ])
            ->assertRedirect(route('front.panel.minecraft-players.show', $mcPlayer));
    }

    public function testLookupPlayerByNonStoredAlias()
    {
        $mcPlayer = MinecraftPlayer::factory()->hasAliases(1)->create();

        $this->mock(MojangPlayerApi::class, function (MockInterface $mock) use ($mcPlayer) {
            $mock->shouldReceive('getUuidOf')->once()->andReturn(
                new MojangPlayer($mcPlayer->uuid, 'Herobrine')
            );
        });

        $this->actingAs($this->adminAccount)
            ->post(route('front.panel.minecraft-players.lookup'), [
                'query' => 'Herobrine',
            ])
            ->assertRedirect(route('front.panel.minecraft-players.show', $mcPlayer));
    }

    public function testLookupOfNonExistentPlayer()
    {
        $this->actingAs($this->adminAccount)
            ->post(route('front.panel.minecraft-players.lookup'), [
                'query' => 'Herobrine',
            ])
            ->assertRedirect(route('front.panel.minecraft-players.index'));
    }
}
