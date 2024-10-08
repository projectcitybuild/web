<?php

use App\Domains\MinecraftRegistration\Notifications\MinecraftRegistrationCodeNotification;
use App\Models\ServerToken;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    $this->endpoint = 'api/v3/minecraft/register';

    $this->validBody = [
        'minecraft_uuid' => '069a79f444e94726a5befca90e38aaf5',
        'minecraft_alias' => 'alias',
        'email' => 'foo@bar.baz',
    ];
});

describe('registration', function () {
    it('requires server token', function () {
        $this->post($this->endpoint)
            ->assertUnauthorized();

        $token = ServerToken::factory()->create();
        $status = $this->withHeaders(['Authorization' => 'Bearer '.$token->token])
            ->post($this->endpoint)
            ->status();

        expect($status)->not->toEqual(401);
    });

    describe('validation errors', function () {
        it('throws if fields are missing', function () {
            $token = ServerToken::factory()->create();
            $this->withHeaders(['Authorization' => 'Bearer '.$token->token])
                ->post($this->endpoint)
                ->assertInvalid(['minecraft_uuid', 'minecraft_alias', 'email']);
        });

        it('throws if minecraft uuid is invalid', function () {
           $token = ServerToken::factory()->create();
           $this->withHeaders(['Authorization' => 'Bearer '.$token->token])
               ->post($this->endpoint, ['minecraft_uuid' => 'invalid uuid'])
               ->assertInvalid(['minecraft_uuid']);
        });

        it('throws if email is invalid', function () {
            $token = ServerToken::factory()->create();
            $this->withHeaders(['Authorization' => 'Bearer '.$token->token])
                ->post($this->endpoint, ['email' => 'invalid email'])
                ->assertInvalid(['email']);
        });
    });

    it('creates a MinecraftRegistration', function () {
        $this->freezeTime(function (Carbon $time) {
            $token = ServerToken::factory()->create();
            $this->withHeaders(['Authorization' => 'Bearer '.$token->token])
                ->post($this->endpoint, $this->validBody);

            $this->assertDatabaseHas('minecraft_registrations', [
                'minecraft_uuid' => $this->validBody['minecraft_uuid'],
                'minecraft_alias' => $this->validBody['minecraft_alias'],
                'email' => $this->validBody['email'],
                'expires_at' => $time->addMinutes(15),
            ]);
        });
    });

    it('accepts any Minecraft UUID format', function ($uuid) {
        $this->validBody['minecraft_uuid'] = $uuid;

        $token = ServerToken::factory()->create();
        $this->withHeaders(['Authorization' => 'Bearer '.$token->token])
            ->post($this->endpoint, $this->validBody);

        $this->assertDatabaseHas('minecraft_registrations', [
            'minecraft_uuid' => '069a79f444e94726a5befca90e38aaf5',
        ]);
    })->with([
        '069a79f4-44e9-4726-a5be-fca90e38aaf5',
        '069a79f444e94726a5befca90e38aaf5',
    ]);

    it('sends an email with a code', function () {
        Notification::fake();

        $token = ServerToken::factory()->create();
        $this->withHeaders(['Authorization' => 'Bearer '.$token->token])
            ->post($this->endpoint, $this->validBody);

        Notification::assertSentOnDemand(
            MinecraftRegistrationCodeNotification::class,
            function (MinecraftRegistrationCodeNotification $notification, array $channels, object $notifiable) {
                return $notifiable->routes['mail'] === $this->validBody['email'];
            }
        );
    });
});
