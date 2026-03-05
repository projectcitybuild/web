<?php

use App\Domains\Pim\Notifications\OpCommandUsedNotification;
use App\Models\CommandAudit;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    Notification::fake();
});

it('stores an op audit and sends a discord notification', function () {
    Config::set('discord.webhook_op_elevation_channel', 'https://discord.test/webhook');

    $response = $this->withServerToken()
        ->postJson('http://api.localhost/v3/server/audit/command/op', [
            'command' => 'op Notch',
            'actor' => 'console',
            'ip' => '127.0.0.1',
            'meta' => '{"foo": "bar"}',
        ]);

    $response->assertStatus(Response::HTTP_CREATED);

    $this->assertDatabaseHas(CommandAudit::class, [
        'command' => '/op Notch',
        'actor' => 'console',
        'ip' => '127.0.0.1',
        'meta' => '{"foo": "bar"}',
    ]);

    Notification::assertSentOnDemand(OpCommandUsedNotification::class);
});

it('fails if actor is invalid', function () {
    Config::set('discord.webhook_op_elevation_channel', 'test');

    $this->withServerToken()
        ->postJson('http://api.localhost/v3/server/audit/command/op', [
            'command' => '/op Notch',
            'actor' => 'player',
            'ip' => '127.0.0.1',
        ])
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonValidationErrors(['actor']);
});

it('fails if meta is invalid json', function () {
    Config::set('discord.webhook_op_elevation_channel', 'test');

    $this->withServerToken()
        ->postJson('http://api.localhost/v3/server/audit/command/op', [
            'command' => '/op Notch',
            'actor' => 'player',
            'ip' => '127.0.0.1',
            'meta' => 'invalid',
        ])
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonValidationErrors(['actor']);
});

it('fails if ip is invalid', function () {
    Config::set('discord.webhook_op_elevation_channel', 'test');

    $this->withServerToken()
        ->postJson('http://api.localhost/v3/server/audit/command/op', [
            'command' => '/op Notch',
            'actor' => 'console',
            'ip' => 'invalid-ip',
        ])
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonValidationErrors(['ip']);
});

it('fails if required fields are missing', function () {
    $this->withServerToken()
        ->postJson('http://api.localhost/v3/server/audit/command/op', [])
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonValidationErrors(['command', 'actor', 'ip']);
});

it('does not double prefix command with slash', function () {
    Config::set('discord.webhook_op_elevation_channel', 'test');

    $this->withServerToken()
        ->postJson('http://api.localhost/v3/server/audit/command/op', [
            'command' => '/op Notch',
            'actor' => 'console',
            'ip' => '127.0.0.1',
        ])
        ->assertCreated();

    $this->assertDatabaseHas(CommandAudit::class, [
        'command' => '/op Notch',
    ]);
});

it('throws if discord webhook channel is missing', function () {
    Config::set('discord.webhook_op_elevation_channel', '');

    $this->withoutExceptionHandling();

    $this->withServerToken()
        ->postJson('http://api.localhost/v3/server/audit/command/op', [
            'command' => '/op Notch',
            'actor' => 'console',
            'ip' => '127.0.0.1',
        ]);
})->throws(
    Exception::class,
    'discord.webhook_op_elevation_channel is missing',
);
