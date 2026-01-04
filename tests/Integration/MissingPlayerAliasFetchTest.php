<?php

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Core\Domains\MinecraftUUID\Data\MinecraftUUIDLookup;
use App\Core\Domains\MinecraftUUID\UseCases\LookupMinecraftUUID;
use App\Domains\Players\Jobs\FetchPlayerAliasJob;
use App\Models\MinecraftPlayer;
use Illuminate\Support\Facades\Queue;
use Mockery\MockInterface;

beforeEach(function () {
    Queue::fake();
});

it('queues job if new player created without alias', function () {
    MinecraftPlayer::factory()->create([
        'alias' => 'foo_bar',
    ]);
    Queue::assertNothingPushed();

    MinecraftPlayer::factory()->create([
        'alias' => '',
    ]);
    Queue::assertPushed(FetchPlayerAliasJob::class);
});

describe('job', function () {
    it('updates alias if lookup returns username', function () {
        $player = MinecraftPlayer::factory()->create(['alias' => '']);
        $result = new MinecraftUUIDLookup(
            username: 'new_alias',
            uuid: new MinecraftUUID($player->uuid),
        );
        $lookup = mock(LookupMinecraftUUID::class, function (MockInterface $mock) use ($player, $result) {
            $mock->shouldReceive('fetch')
                ->with($player->uuid)
                ->once()
                ->andReturn($result);
        });
        $job = new FetchPlayerAliasJob($player);
        $job->handle($lookup);

        expect($player->refresh()->alias)->toEqual('new_alias');
    });

    it('does nothing if lookup returns empty username', function () {
        $player = MinecraftPlayer::factory()->create(['alias' => '']);
        $result = new MinecraftUUIDLookup(
            username: '',
            uuid: new MinecraftUUID($player->uuid),
        );
        $lookup = mock(LookupMinecraftUUID::class, function (MockInterface $mock) use ($player, $result) {
            $mock->shouldReceive('fetch')
                ->with($player->uuid)
                ->once()
                ->andReturn($result);
        });
        $job = new FetchPlayerAliasJob($player);
        $job->handle($lookup);

        expect($player->refresh()->alias)->toEqual('');
    });

    it('does nothing if lookup returns nothing', function () {
        $player = MinecraftPlayer::factory()->create(['alias' => '']);
        $lookup = mock(LookupMinecraftUUID::class, function (MockInterface $mock) use ($player) {
            $mock->shouldReceive('fetch')
                ->with($player->uuid)
                ->once()
                ->andReturn(null);
        });
        $job = new FetchPlayerAliasJob($player);
        $job->handle($lookup);

        expect($player->refresh()->alias)->toEqual('');
    });
});
