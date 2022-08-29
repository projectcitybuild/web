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

    public function test_cannot_see_login_signed_in()
    {
        $this->actingAs($this->account)
            ->get(route('front.login'))
            ->assertRedirect(route('front.account.settings'));
    }

    public function test_user_cannot_log_in_with_unactivated_account()
    {
        $account = Account::factory()->unactivated()->create();

        $this->post(route('front.login.submit'), [
            'email' => $account->email,
            'password' => 'secret',
        ])->assertSessionHasErrors();
    }

    public function test_user_is_shown_error_with_wrong_credentials()
    {
        $this->post(route('front.login.submit'), [
            'email' => $this->account->email,
            'password' => 'wrong',
        ]);

        $this->assertGuest();
    }

    public function test_if_user_does_not_enter_email()
    {
        $this->post(route('front.login.submit'), [
            'email' => '',
            'password' => 'secret',
        ]);

        $this->assertGuest();
    }

    public function test_if_user_does_not_enter_password()
    {
        $this->post(route('front.login.submit'), [
            'email' => $this->account->email,
            'password' => '',
        ]);

        $this->assertGuest();
    }

    public function test_redirect_back_to_intended_page()
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

    public function test_last_login_details_updated()
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
