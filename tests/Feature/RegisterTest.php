<?php

namespace Tests\Feature;

use Entities\Models\Eloquent\Account;
use Entities\Models\Eloquent\Group;
use Entities\Notifications\AccountActivationNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Library\Recaptcha\Validator\Adapters\GoogleRecaptchaValidator;
use Library\Recaptcha\Validator\RecaptchaValidator;
use Library\SignedURL\Adapters\LaravelSignedURLGenerator;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    private function withRealRecaptcha(): self
    {
        $this->app->bind(RecaptchaValidator::class, GoogleRecaptchaValidator::class);

        return $this;
    }

    private function withRequiredFormFields(Account $account): array
    {
        return array_merge($account->toArray(), [
            'password_confirm' => 'password',
            'g-recaptcha-response' => Str::random(),
            'terms' => 1,
        ]);
    }

    public function test_cannot_see_register_signed_in()
    {
        $this->actingAs(Account::factory()->create())
            ->get(route('front.register'))
            ->assertRedirect(route('front.account.settings'));
    }

    public function test_user_can_register()
    {
        Group::factory()->create(['is_default' => true]);

        $unactivatedAccount = Account::factory()
            ->passwordUnhashed()
            ->unactivated()
            ->make();

        $this->post(route('front.register.submit'), $this->withRequiredFormFields($unactivatedAccount))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('accounts', [
            'email' => $unactivatedAccount->email,
            'username' => $unactivatedAccount->username,
            'activated' => false,
        ]);
    }

    public function test_recaptcha_field_is_required()
    {
        $unactivatedAccount = Account::factory()
            ->passwordUnhashed()
            ->unactivated()
            ->make();

        $this->withRealRecaptcha()
            ->post(route('front.register.submit'), $this->withRequiredFormFields($unactivatedAccount))
            ->assertSessionHasErrors('g-recaptcha-response');
    }

    public function test_recaptcha_field_is_validated()
    {
        $unactivatedAccount = Account::factory()
            ->passwordUnhashed()
            ->unactivated()
            ->make();

        $this->withRealRecaptcha()
            ->post(route('front.register.submit'), $this->withRequiredFormFields($unactivatedAccount))
            ->assertSessionHasErrors('g-recaptcha-response');
    }

    public function test_user_cannot_register_with_same_email_as_other_account()
    {
        $existingAccount = Account::factory()->create();

        $newAccount = Account::factory()
            ->passwordUnhashed()
            ->make(['email' => $existingAccount->email]);

        $this->post(route('front.register.submit'), $this->withRequiredFormFields($newAccount))
            ->assertSessionHasErrors();
    }

    public function test_user_cannot_register_with_same_username_as_other_account()
    {
        $existingAccount = Account::factory()->create();

        $newAccount = Account::factory()
            ->passwordUnhashed()
            ->make(['username' => $existingAccount->username]);

        $this->post(route('front.register.submit'), $this->withRequiredFormFields($newAccount))
            ->assertSessionHasErrors();
    }

    public function test_assert_password_is_hashed()
    {
        $unactivatedAccount = Account::factory()
            ->passwordUnhashed()
            ->make();

        $this->post(route('front.register.submit'), $this->withRequiredFormFields($unactivatedAccount))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseMissing('accounts', [
            'email' => $unactivatedAccount->email,
            'password' => $unactivatedAccount->password,
        ]);
    }

    public function test_new_member_is_put_in_default_group()
    {
        $memberGroup = Group::create([
            'name' => 'member',
            'is_default' => 1,
        ]);

        $unactivatedAccount = Account::factory()
            ->passwordUnhashed()
            ->unactivated()
            ->make();

        $this->post(route('front.register.submit'), $this->withRequiredFormFields($unactivatedAccount))
            ->assertSessionHasNoErrors();

        $account = Account::where('email', $unactivatedAccount['email'])->firstOrFail();

        $this->assertDatabaseHas('groups_accounts', [
            'group_id' => $memberGroup->group_id,
            'account_id' => $account->account_id,
        ]);
    }

    public function test_user_is_sent_verification_mail()
    {
        Notification::fake();

        Group::factory()->create(['is_default' => true]);

        $unactivatedAccount = Account::factory()
            ->passwordUnhashed()
            ->unactivated()
            ->make();

        $this->post(route('front.register.submit'), $this->withRequiredFormFields($unactivatedAccount))
            ->assertSuccessful()
            ->assertSessionDoesntHaveErrors();

        Notification::assertSentTo(Account::first(), AccountActivationNotification::class);
    }

    public function test_user_can_verify_email()
    {
        $unactivatedAccount = Account::factory()->unactivated()->create();

        // TODO: find way to do this without manually creating the URL here
        $signedURLGenerator = new LaravelSignedURLGenerator();
        $activationURL = $signedURLGenerator->makeTemporary(
            routeName: 'front.register.activate',
            expiresAt: now()->addDay(),
            parameters: ['email' => $unactivatedAccount->email],
        );

        $this->get($activationURL)
            ->assertSuccessful();

        $this->assertEquals(true, Account::first()->activated);
    }

    public function test_user_is_redirected_to_intent_after_verification()
    {
        Session::put('url.intended', '/my/path');

        $unactivatedAccount = Account::factory()->unactivated()->create();

        // TODO: find way to do this without manually creating the URL here
        $signedURLGenerator = new LaravelSignedURLGenerator();
        $activationURL = $signedURLGenerator->makeTemporary(
            routeName: 'front.register.activate',
            expiresAt: now()->addDay(),
            parameters: ['email' => $unactivatedAccount->email],
        );

        $this->get($activationURL)
            ->assertRedirect('/my/path');
    }
}
