<?php

use App\Domains\MinecraftEventBus\Events\IpAddressBanned;
use App\Domains\MinecraftEventBus\Events\MinecraftPlayerUpdated;
use App\Domains\MinecraftEventBus\Events\MinecraftUuidBanned;
use App\Domains\MinecraftEventBus\UseCases\PostEventToServer;
use App\Models\GameIPBan;
use App\Models\GamePlayerBan;
use App\Models\MinecraftPlayer;
use App\Models\Server;
use App\Models\ServerToken;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Mockery\MockInterface;

it('sends post request to given server', function () {
    Http::fake();

    $server = Server::factory()->create([
        'ip' => '192.168.0.1',
        'port' => '25565',
    ]);
    ServerToken::factory()->create([
        'server_id' => $server->getKey(),
        'token' => '123',
    ]);
    $payload = ['foo' => 'bar'];

    (new PostEventToServer())->send(
        server: $server,
        path: 'foo/bar',
        payload: $payload,
    );

    Http::assertSent(function (Request $request) {
        return $request->hasHeader('Authorization', 'Bearer 123') &&
            $request->url() == 'http://192.168.0.1:25565/foo/bar' &&
            $request->method() == 'POST' &&
            $request['foo'] == 'bar';
    });
});

it('is dispatched on MinecraftPlayerUpdated event', function () {
    Server::factory()->create();
    $player = MinecraftPlayer::factory()->create();

    $this->mock(PostEventToServer::class, function (MockInterface $mock) use ($player) {
        $mock->shouldReceive('send')
            ->with(anyArgs(), 'events/player/sync', ['uuid' => $player->uuid])
            ->once()
            ->andReturn();
    });

    MinecraftPlayerUpdated::dispatch($player);
});

it('is dispatched on MinecraftUuidBanned event', function () {
    $server = Server::factory()->create();
    $player = MinecraftPlayer::factory()->create();
    $ban = GamePlayerBan::factory()
        ->server($server)
        ->bannedPlayer($player)
        ->create();

    $this->mock(PostEventToServer::class, function (MockInterface $mock) use ($ban) {
        $mock->shouldReceive('send')
            ->with(anyArgs(), 'events/ban/uuid', $ban->toArray())
            ->once()
            ->andReturn();
    });

    MinecraftUuidBanned::dispatch($ban);
});

it('is dispatched on IpAddressBanned event', function () {
    Server::factory()->create();
    $player = MinecraftPlayer::factory()->create();
    $ban = GameIPBan::factory()->bannedBy($player)->create();

    $this->mock(PostEventToServer::class, function (MockInterface $mock) use ($ban) {
        $mock->shouldReceive('send')
            ->with(anyArgs(), 'events/ban/ip', $ban->toArray())
            ->once()
            ->andReturn();
    });

    IpAddressBanned::dispatch($ban);
});
