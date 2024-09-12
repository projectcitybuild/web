<?php

use App\Core\Domains\Tokens\TokenGenerator;
use App\Domains\PasswordReset\Notifications\AccountPasswordResetCompleteNotification;
use App\Domains\PasswordReset\Notifications\AccountPasswordResetNotification;
use App\Models\Account;
use App\Models\PasswordReset;
use Illuminate\Support\Facades\Notification;
use Mockery\MockInterface;

beforeEach(function () {
    $this->requestEndpoint = route('front.password-reset.store');
    $this->setPasswordEndpoint = route('front.password-reset.update');

    $this->account = Account::factory()->create();

    Notification::fake();
});

describe('submit', function () {
    it('throws a validation exception if account not found', function () {
        $this->post($this->requestEndpoint, ['email' => 'missing@foo.bar'])
            ->assertInvalid('email');
    });

    it('sends password reset email', function () {
        Notification::assertNothingSent();

        $this->post($this->requestEndpoint, ['email' => $this->account->email])
            ->assertRedirect();

        Notification::assertSentTo($this->account, AccountPasswordResetNotification::class);
    });

    it('saves an associated token', function () {
        $token = 'token';
        $this->mock(TokenGenerator::class, function (MockInterface $mock) use ($token) {
            $mock->shouldReceive('make')
                ->once()
                ->andReturn($token);
        });

        $this->post($this->requestEndpoint, ['email' => $this->account->email]);

        $this->assertDatabaseHas(PasswordReset::tableName(), [
            'email' => $this->account->email,
            'token' => $token,
            'account_id' => $this->account->getKey(),
        ]);
    });
});

describe('set new password', function () {
    beforeEach(function () {
        $this->reset = PasswordReset::factory()
            ->create(['email' => $this->account->email]);
    });

    it('changes the account password', function () {
        $originalPassword = $this->account->password;

        $this->patch($this->setPasswordEndpoint, [
            'password_token' => $this->reset->token,
            'password' => 'new_password_1234',
            'password_confirm' => 'new_password_1234',
        ])->assertSessionHasNoErrors();

        $this->account->refresh();

        expect($this->account->password)
            ->not
            ->toBe($originalPassword);
    });

    it('sends password updated email', function () {
        Notification::assertNothingSent();

        $this->patch($this->setPasswordEndpoint, [
            'password_token' => $this->reset->token,
            'password' => 'new_password_1234',
            'password_confirm' => 'new_password_1234',
        ]);

        Notification::assertSentTo($this->account, AccountPasswordResetCompleteNotification::class);
    });

    it('redirects to login page with a message', function () {
        $this->patch($this->setPasswordEndpoint, [
            'password_token' => $this->reset->token,
            'password' => 'new_password_1234',
            'password_confirm' => 'new_password_1234',
        ])
            ->assertRedirectToRoute('front.login')
            ->assertSessionHas('success');
    });
});
