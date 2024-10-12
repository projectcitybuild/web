<?php

use App\Domains\MinecraftRegistration\Notifications\MinecraftRegistrationCodeNotification;
use App\Domains\MinecraftRegistration\Notifications\MinecraftRegistrationCompleteNotification;
use App\Models\Account;
use App\Models\MinecraftPlayer;
use App\Models\MinecraftRegistration;
use App\Models\ServerToken;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    $this->endpoint = 'api/v3/minecraft/register';
});

it('requires server token', function () {
    $this->post($this->endpoint)
        ->assertUnauthorized();

    $status = $this->withServerToken()
        ->put($this->endpoint)
        ->status();

    expect($status)->not->toEqual(401);
});

describe('validation errors', function () {
    it('throws if fields are missing', function () {
        $this->withServerToken()
            ->put($this->endpoint)
            ->assertInvalid(['minecraft_uuid', 'code']);
    });

    it('throws if minecraft uuid is invalid', function () {
        $this->withServerToken()
            ->put($this->endpoint, ['minecraft_uuid' => 'invalid uuid'])
            ->assertInvalid(['minecraft_uuid']);
    });
});

describe('validity check', function () {
    it('throws 404 if not found', function () {
        $this->withServerToken()
            ->put($this->endpoint, [
                'minecraft_uuid' => '069a79f444e94726a5befca90e38aaf5',
                'code' => '123456',
            ])
            ->assertStatus(404);
    });

    it('throws 410 if expired', function () {
        $registration = MinecraftRegistration::factory()
            ->expired()
            ->create();

        $this->withServerToken()
            ->put($this->endpoint, [
                'minecraft_uuid' => $registration->minecraft_uuid,
                'code' => $registration->code,
            ])
            ->assertStatus(410);
    });
});

describe('valid request', function () {
    it('creates activated Account', function () {
        $registration = MinecraftRegistration::factory()->create();

        $this->assertDatabaseMissing('accounts', [
            'email' => $registration->email,
            'activated' => true,
            'username' => $registration->minecraft_alias,
        ]);

        $this->withServerToken()
            ->put($this->endpoint, [
                'minecraft_uuid' => $registration->minecraft_uuid,
                'code' => $registration->code,
            ]);

        $this->assertDatabaseHas('accounts', [
            'email' => $registration->email,
            'activated' => true,
            'username' => $registration->minecraft_alias,
        ]);
    });

    it('activates existing Account with same email address', function () {
        $registration = MinecraftRegistration::factory()->create();
        Account::factory()->unactivated()->create([
            'email' => $registration->email,
            'username' => 'foobar',
        ]);

        $this->assertDatabaseHas('accounts', [
            'email' => $registration->email,
            'activated' => false,
            'username' => 'foobar',
        ]);

        $this->withServerToken()
            ->put($this->endpoint, [
                'minecraft_uuid' => $registration->minecraft_uuid,
                'code' => $registration->code,
            ]);

        $this->assertDatabaseHas('accounts', [
            'email' => $registration->email,
            'activated' => true,
            'username' => 'foobar',
        ]);
    });

    it('creates Account with unique username', function () {
        $registration = MinecraftRegistration::factory()->create([
            'minecraft_alias' => 'foobar',
        ]);
        Account::factory()->create(['username' => 'foobar']);
        Account::factory()->create(['username' => 'foobar_1']);
        Account::factory()->create(['username' => 'foobar_2']);

        $this->assertDatabaseMissing('accounts', ['username' => 'foobar_3']);

        $this->withServerToken()
            ->put($this->endpoint, [
                'minecraft_uuid' => $registration->minecraft_uuid,
                'code' => $registration->code,
            ]);

        $this->assertDatabaseHas('accounts', ['username' => 'foobar_3']);
    });

    it('creates MinecraftPlayer', function () {
        $registration = MinecraftRegistration::factory()->create();

        $this->assertDatabaseMissing('players_minecraft', [
            'uuid' => $registration->minecraft_uuid,
            'alias' => $registration->minecraft_alias,
        ]);

        $this->withServerToken()
            ->put($this->endpoint, [
                'minecraft_uuid' => $registration->minecraft_uuid,
                'code' => $registration->code,
            ]);

        $account = Account::whereEmail($registration->email)->firstOrFail();
        expect($account)->not->toBeNull();

        $player = MinecraftPlayer::where('uuid', $registration->minecraft_uuid)
            ->where('alias', $registration->minecraft_alias)
            ->first();

        expect($player)->not->toBeNull();
        expect($player->account_id)->toEqual($account->getKey());
    });

    it('updates existing MinecraftPlayer', function () {
        $registration = MinecraftRegistration::factory()->create();
        MinecraftPlayer::factory()->create([
            'uuid' => $registration->minecraft_uuid,
            'alias' => 'old_alias',
            'account_id' => null,
        ]);

        $this->assertDatabaseHas('players_minecraft', [
            'uuid' => $registration->minecraft_uuid,
            'alias' => 'old_alias',
            'account_id' => null,
        ]);

        $this->withServerToken()
            ->put($this->endpoint, [
                'minecraft_uuid' => $registration->minecraft_uuid,
                'code' => $registration->code,
            ]);

        $account = Account::whereEmail($registration->email)->firstOrFail();
        expect($account)->not->toBeNull();

        $this->assertDatabaseHas('players_minecraft', [
            'uuid' => $registration->minecraft_uuid,
            'alias' => $registration->minecraft_alias,
            'account_id' => $account->getKey(),
        ]);
    });

    it('deletes MinecraftRegistration', function () {
        $registration = MinecraftRegistration::factory()->create();

        $this->assertDatabaseHas('minecraft_registrations', [
            'minecraft_uuid' => $registration->minecraft_uuid,
        ]);

        $this->withServerToken()
            ->put($this->endpoint, [
                'minecraft_uuid' => $registration->minecraft_uuid,
                'code' => $registration->code,
            ]);

        $this->assertDatabaseMissing('minecraft_registrations', [
            'minecraft_uuid' => $registration->minecraft_uuid,
        ]);
    });

    it('sends welcome email', function () {
        Notification::fake();

        $registration = MinecraftRegistration::factory()->create();

        $this->withServerToken()
            ->put($this->endpoint, [
                'minecraft_uuid' => $registration->minecraft_uuid,
                'code' => $registration->code,
            ]);

        $account = Account::whereEmail($registration->email)->firstOrFail();

        Notification::assertSentTo($account, MinecraftRegistrationCompleteNotification::class);
    });
});
