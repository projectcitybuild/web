<?php

namespace Tests\Feature;

use Entities\Models\Eloquent\Account;
use Tests\TestCase;

class LoginTest extends TestCase
{
    private Account $account;

    protected function setUp(): void
    {
        parent::setUp();

        $this->account = Account::factory()->create();
    }

    public function testCannotSeeLoginSignedIn()
    {
        $this->actingAs($this->account)
            ->get(route('front.login'))
            ->assertRedirect(route('front.account.settings'));
    }

    public function testUserCannotLogInWithUnactivatedAccount()
    {
        $account = Account::factory()->unactivated()->create();

        $this->post(route('front.login.submit'), [
            'email' => $account->email,
            'password' => 'secret',
        ])->assertSessionHasErrors();
    }

    public function testUserIsShownErrorWithWrongCredentials()
    {
        $this->post(route('front.login.submit'), [
            'email' => $this->account->email,
            'password' => 'wrong',
        ]);

        $this->assertGuest();
    }

    public function testIfUserDoesNotEnterEmail()
    {
        $this->post(route('front.login.submit'), [
            'email' => '',
            'password' => 'secret',
        ]);

        $this->assertGuest();
    }

    public function testIfUserDoesNotEnterPassword()
    {
        $this->post(route('front.login.submit'), [
            'email' => $this->account->email,
            'password' => '',
        ]);

        $this->assertGuest();
    }

    public function testRedirectBackToIntendedPage()
    {
        $this->get(route('front.account.settings'))
            ->assertRedirect(route('front.login'));

        $this->withoutExceptionHandling();

        $this->post(route('front.login.submit'), [
            'email' => $this->account->email,
            'password' => 'secret',
        ])
            ->assertRedirect(route('front.account.settings'));
    }

    public function testLastLoginDetailsUpdated()
    {
        $oldLoginTime = $this->account->last_login_at;
        $oldLoginIp = $this->account->last_login_ip;

        $this->post(route('front.login.submit'), [
            'email' => $this->account->email,
            'password' => 'secret',
        ])->assertSessionHasNoErrors();

        $this->account->refresh();

        $this->assertNotEquals($oldLoginIp, $this->account->last_login_ip);
        $this->assertNotEquals($oldLoginTime, $this->account->last_login_at);
    }
}
