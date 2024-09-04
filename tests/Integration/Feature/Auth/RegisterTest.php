<?php

use App\Core\Domains\Captcha\Validator\CaptchaValidator;
use App\Domains\Activation\Notifications\AccountNeedsActivationNotification;
use App\Models\Account;
use App\Models\Group;
use Illuminate\Support\Facades\Notification;
use Mockery\MockInterface;

function unactivatedAccount(array $attributes = []): Account
{
    return Account::factory()
        ->unactivated()
        ->make($attributes);
}

function validFormDataFor(Account $account): array
{
    return array_merge($account->toArray(), [
        'captcha-response' => 'captcha',
        'terms' => 1,
    ]);
}

beforeEach(function () {
    $this->formEndpoint = route('front.register');
    $this->submitEndpoint = route('front.register.submit');

    $this->allFieldKeys = [
        'email',
        'username',
        'password',
        'captcha-response',
        'terms',
    ];
});

it('redirects to account settings if logged in', function () {
    $this->actingAs(Account::factory()->create())
        ->get($this->formEndpoint)
        ->assertRedirect(route('front.account.settings'));
});

it('shows a page', function () {
    $this->get($this->formEndpoint)
        ->assertSuccessful();
});

describe('validation errors', function () {
    it('throws if fields are missing', function () {
        $this->post($this->submitEndpoint, [])
            ->assertInvalid($this->allFieldKeys);
    });

    it('throws if password is too short', function () {
        $unactivatedAccount = unactivatedAccount();
        $formData = validFormDataFor($unactivatedAccount);
        $formData['password'] = '123';

        $this->post($this->submitEndpoint, $formData)
            ->assertInvalid(['password']);
    });

    it('throws if username is already in use', function () {
        Account::factory()->create(['username' => 'taken']);
        $this->assertDatabaseHas('accounts', ['username' => 'taken']);

        $unactivatedAccount = unactivatedAccount(['username' => 'taken']);
        $formData = validFormDataFor($unactivatedAccount);

        $this->post($this->submitEndpoint, $formData)
            ->assertInvalid(['username']);
    });

    it('throws if email is already in use', function () {
        Account::factory()->create(['email' => 'me@pcbmc.co']);
        $this->assertDatabaseHas('accounts', ['email' => 'me@pcbmc.co']);

        $unactivatedAccount = unactivatedAccount(['email' => 'me@pcbmc.co']);
        $formData = validFormDataFor($unactivatedAccount);

        $this->post($this->submitEndpoint, $formData)
            ->assertInvalid(['email']);
    });

    it('throws if captcha is invalid', function () {
        $this->mock(CaptchaValidator::class, function (MockInterface $mock) {
            $mock->shouldReceive('passed')
                ->once()
                ->andReturn(false);
        });
        $unactivatedAccount = unactivatedAccount();
        $formData = validFormDataFor($unactivatedAccount);

        $this->post($this->submitEndpoint, $formData)
            ->assertInvalid(['captcha-response']);
    });
});

describe('successful submit', function () {
    beforeEach(function () {
        Group::factory()->create(['is_default' => true]);

        $this->unactivatedAccount = unactivatedAccount();
        $this->formData = validFormDataFor($this->unactivatedAccount);
    });

    it('creates an unactivated account from valid form data', function () {
        $this->post($this->submitEndpoint, $this->formData)
            ->assertSessionHasNoErrors()
            ->assertValid($this->allFieldKeys);

        $this->assertDatabaseHas('accounts', [
            'email' => $this->unactivatedAccount->email,
            'username' => $this->unactivatedAccount->username,
            'activated' => false,
        ]);
    });

    it('hashes the password', function () {
        $this->post($this->submitEndpoint, $this->formData);

        $account = Account::where('email', $this->unactivatedAccount->email)->firstOrFail();

        expect($account->password)
            ->not
            ->toBe($this->unactivatedAccount->password);
    });

    it('assigns account to default group', function () {
        $defaultGroup = Group::where('is_default', true)->firstOrFail();

        $this->post($this->submitEndpoint, $this->formData);

        $account = Account::where('email', $this->unactivatedAccount->email)->firstOrFail();

        $this->assertDatabaseHas('groups_accounts', [
            'group_id' => $defaultGroup->group_id,
            'account_id' => $account->account_id,
        ]);
    });

    it('sends an email to activate the account', function () {
        Notification::fake();

        $this->post($this->submitEndpoint, $this->formData);

        $account = Account::where('email', $this->unactivatedAccount->email)->firstOrFail();

        Notification::assertSentTo($account, AccountNeedsActivationNotification::class);
    });

    it('authenticates as user', function () {
        $this->assertGuest();
        $this->post($this->submitEndpoint, $this->formData);

        $account = Account::whereEmail($this->formData['email'])->firstOrFail();
        $this->assertAuthenticatedAs($account);
    });

    it('redirects to activation flow', function () {
        $this->post($this->submitEndpoint, $this->formData)
            ->assertRedirect(route('front.activate'));
    });
});
