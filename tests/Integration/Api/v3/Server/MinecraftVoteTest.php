<?php

use App\Core\Domains\MinecraftUUID\Data\MinecraftUUID;
use App\Domains\Players\Jobs\FetchPlayerAliasJob;
use App\Models\MinecraftPlayer;
use App\Models\MinecraftVote;
use App\Models\Server;
use App\Models\ServerToken;
use Illuminate\Support\Facades\Queue;

it('requires a server token', function () {
    $this->postJson('http://api.localhost/v3/server/votes')
        ->assertUnauthorized();
});

it('validates vote data', function () {
    $this->withServerToken()
        ->postJson('http://api.localhost/v3/server/votes', [])
        ->assertInvalid(['event_id', 'player_uuid', 'service_name', 'address', 'timestamp']);
});

it('stores vote data with the authenticated server and associated player', function () {
    $this->freezeTime(function ($receivedAt) {
        $server = Server::factory()->create();
        $serverToken = ServerToken::factory()->create(['server_id' => $server->id]);
        $player = MinecraftPlayer::factory()->create(['alias' => 'PreviousName']);

        $this->withServerToken($serverToken)
            ->postJson('http://api.localhost/v3/server/votes', [
                'event_id' => '8a59bca2-3904-4a12-a146-0f73499e786d',
                'player_uuid' => $player->uuid,
                'username' => 'PlayerOne',
                'service_name' => 'ExampleVoteSite',
                'address' => '203.0.113.10',
                'timestamp' => 1735689600123,
                'additional_data' => 'dm90ZS1tZXRhZGF0YQ==',
                'server_id' => 999,
            ])
            ->assertCreated()
            ->assertJsonStructure(['id']);

        $this->assertDatabaseHas(MinecraftVote::tableName(), [
            'server_id' => $server->id,
            'player_id' => $player->id,
            'event_id' => '8a59bca2-3904-4a12-a146-0f73499e786d',
            'service_name' => 'ExampleVoteSite',
            'address' => '203.0.113.10',
            'source_timestamp' => 1735689600123,
            'voted_at' => '2025-01-01 00:00:00',
            'additional_data' => 'dm90ZS1tZXRhZGF0YQ==',
            'created_at' => $receivedAt,
        ]);

        $this->assertDatabaseHas(MinecraftPlayer::tableName(), [
            'id' => $player->id,
            'alias' => 'PlayerOne',
        ]);
    });
});

it('creates a player without an alias when username is omitted', function () {
    $uuid = MinecraftUUID::random()->trimmed();
    Queue::fake([FetchPlayerAliasJob::class]);

    $this->withServerToken()
        ->postJson('http://api.localhost/v3/server/votes', [
            'event_id' => 'f029f4af-386b-4faa-8bed-bd02c16e99fe',
            'player_uuid' => $uuid,
            'service_name' => 'ExampleVoteSite',
            'address' => '203.0.113.10',
            'timestamp' => 1735689600123,
        ])
        ->assertCreated();

    $player = MinecraftPlayer::where('uuid', $uuid)->firstOrFail();

    expect($player->alias)->toBeNull();
    Queue::assertPushed(FetchPlayerAliasJob::class);
    $this->assertDatabaseHas(MinecraftVote::tableName(), [
        'player_id' => $player->id,
    ]);
});

it('returns the original vote for a retried event ID', function () {
    Queue::fake([FetchPlayerAliasJob::class]);
    $serverToken = ServerToken::factory()->create();
    $payload = [
        'event_id' => 'e630e862-12e4-4bb7-896c-531959fb1583',
        'player_uuid' => MinecraftUUID::random()->trimmed(),
        'service_name' => 'ExampleVoteSite',
        'address' => '203.0.113.10',
        'timestamp' => 1735689600123,
    ];

    $response = $this->withServerToken($serverToken)
        ->postJson('http://api.localhost/v3/server/votes', $payload)
        ->assertCreated();

    $this->withServerToken($serverToken)
        ->postJson('http://api.localhost/v3/server/votes', $payload)
        ->assertOk()
        ->assertJsonPath('id', $response->json('id'));

    Queue::assertPushed(FetchPlayerAliasJob::class, 1);
    $this->assertDatabaseCount(MinecraftVote::tableName(), 1);
});

it('rejects invalid timestamps without recording a vote', function () {
    $this->withServerToken()
        ->postJson('http://api.localhost/v3/server/votes', [
            'event_id' => '324e70cb-ad4b-46c3-b9e0-cfc3aa1ac6d5',
            'player_uuid' => MinecraftUUID::random()->trimmed(),
            'service_name' => 'ExampleVoteSite',
            'address' => '203.0.113.10',
            'timestamp' => 'not-a-timestamp',
        ])
        ->assertInvalid(['timestamp']);

    $this->assertDatabaseCount(MinecraftVote::tableName(), 0);
});
