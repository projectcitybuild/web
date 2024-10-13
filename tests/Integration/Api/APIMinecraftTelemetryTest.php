<?php

use App\Models\MinecraftPlayer;
use Illuminate\Support\Carbon;

beforeEach(function () {
    $this->endpoint = 'api/v2/minecraft/telemetry/seen';
});

it('requires server token', function () {
    $body = ['uuid' => 'uuid', 'alias' => 'alias'];

    $this->post($this->endpoint, $body)
        ->assertUnauthorized();

    $status = $this->withServerToken()
        ->post($this->endpoint, $body)
        ->status();

    expect($status)->not->toEqual(401);
});

it('validates input', function () {
    $this->withServerToken()
        ->post(uri: $this->endpoint, data: [])
        ->assertInvalid(['alias', 'uuid']);

    $this->withServerToken()
        ->post(uri: $this->endpoint, data: [
            'uuid' => '069a79f444e94726a5befca90e38aaf5',
            'alias' => 'alias',
        ])
        ->assertOk();
});

it('updates last seen date', function () {
    $this->freezeTime(function (Carbon $time) {
        $player = MinecraftPlayer::factory()->create([
            'uuid' => '069a79f444e94726a5befca90e38aaf5',
            'last_seen_at' => $time->copy()->subWeek(),
        ]);

        $this->withServerToken()
            ->post(uri: $this->endpoint, data: [
                'uuid' => '069a79f444e94726a5befca90e38aaf5',
                'alias' => 'alias',
            ])
            ->assertOk();

        $this->assertDatabaseHas(
            table: MinecraftPlayer::tableName(),
            data: [
                'player_minecraft_id' => $player->getKey(),
                'uuid' => '069a79f444e94726a5befca90e38aaf5',
                'alias' => 'alias',
                'last_seen_at' => $time,
            ],
        );
    });
});
