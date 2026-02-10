<?php

use App\Domains\MinecraftRegistration\Notifications\MinecraftRegistrationCodeNotification;
use App\Models\MinecraftRegistration;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    $this->validBody = [
        'uuid' => '069a79f444e94726a5befca90e38aaf5',
        'alias' => 'alias',
        'email' => 'foo@bar.baz',
    ];
});

it('requires server token', function () {
    $this->post('http://api.localhost/v3/server/register', [
        'uuid' => '069a79f444e94726a5befca90e38aaf5',
    ])
        ->assertUnauthorized();

    $status = $this->withServerToken()
        ->post('http://api.localhost/v3/server/register', [
            'uuid' => '069a79f444e94726a5befca90e38aaf5',
        ])
        ->status();

    expect($status)->not->toEqual(401);
});

describe('validation errors', function () {
    it('throws if fields are missing', function () {
        $this->withServerToken()
            ->post('http://api.localhost/v3/server/register', [
                'uuid' => '069a79f444e94726a5befca90e38aaf5',
            ])
            ->assertInvalid(['alias', 'email']);
    });

    it('throws if minecraft uuid is invalid', function () {
        $this->withServerToken()
            ->post('http://api.localhost/v3/server/register', [
                'uuid' => 'invalid_uuid',
            ])
            ->assertInvalid(['uuid']);
    });

    it('throws if email is invalid', function () {
        $this->withServerToken()
            ->post('http://api.localhost/v3/server/register', [
                'uuid' => '069a79f444e94726a5befca90e38aaf5',
                'email' => 'invalid email',
            ])
            ->assertInvalid(['email']);
    });
});

it('creates a MinecraftRegistration', function () {
    $this->freezeTime(function (Carbon $time) {
        $this->withServerToken()
            ->post('http://api.localhost/v3/server/register', $this->validBody)
            ->assertSuccessful();

        $this->assertDatabaseHas(MinecraftRegistration::tableName(), [
            'minecraft_uuid' => $this->validBody['uuid'],
            'minecraft_alias' => $this->validBody['alias'],
            'email' => $this->validBody['email'],
            'expires_at' => $time->addHour(),
        ]);
    });
});

it('accepts any Minecraft UUID format', function ($uuid) {
    $this->validBody['uuid'] = $uuid;

    $this->withServerToken()
        ->post('http://api.localhost/v3/server/register', $this->validBody)
        ->assertSuccessful();
})->with([
    '069a79f4-44e9-4726-a5be-fca90e38aaf5',
    '069a79f444e94726a5befca90e38aaf5',
]);

it('sends an email with a code', function () {
    Notification::fake();

    $this->withServerToken()
        ->post('http://api.localhost/v3/server/register', $this->validBody);

    Notification::assertSentOnDemand(
        MinecraftRegistrationCodeNotification::class,
        function (MinecraftRegistrationCodeNotification $notification, array $channels, object $notifiable) {
            return $notifiable->routes['mail'] === $this->validBody['email'];
        }
    );
});
