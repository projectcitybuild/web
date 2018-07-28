<?php
namespace Tests\Integration;

use Domains\Modules\Accounts\Models\Account;
use Domains\Modules\Accounts\Models\AccountPasswordReset;
use Domains\Modules\Accounts\Models\UnactivatedAccount;
use Domains\Modules\Accounts\Notifications\AccountPasswordResetNotification;
use Domains\Library\Recaptcha\RecaptchaRule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PasswordReset_Render_Test extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;

    private function getValidEmailInput() : array
    {
        return [
            'email'                 => 'test_email@projectcitybuild.com',
            'g-recaptcha-response'  => 'test',
        ];
    }

    private function createAccount() : Account
    {
        return Account::create([
            'email'     => 'test_email@projectcitybuild.com',
            'password'  => Hash::make('test'),
        ]);
    }

    private function createPasswordReset() : AccountPasswordReset
    {
        return AccountPasswordReset::create([
            'email' => 'test_reset@projectcitybuild.com',
            'token' => 'test_token',
        ]);
    }


    protected function setUp()
    {
        parent::setUp();

        // prevent real emails being sent
        Notification::fake();

        // disable recaptcha in registration tests
        RecaptchaRule::disable();
    }

    public function testDoesRender()
    {
        $response = $this->get('password-reset');
        $response->assertStatus(200);
    }

    public function testEmailForm_acceptsValidInput()
    {
        // given...
        $this->createAccount();
        $data = $this->getValidEmailInput();
        $this->get('password-reset');

        // when...
        $response = $this->post('password-reset', $data);

        // expect...
        $response->assertRedirect('password-reset');
        $response->assertSessionHas('success');
    }

    public function testEmailForm_noAccount_hasError()
    {
        // given...
        $data = ['email' => 'non_existent_account@projectcitybuild.com'];

        // when...
        $response = $this->post('password-reset', $data);

        // expect...
        $response->assertSessionHasErrors('email');
    }

    public function testEmailForm_sendsResetEmail()
    {
        // given...
        $account = $this->createAccount();
        $data = $this->getValidEmailInput();

        // when...
        $this->post('password-reset', $data);

        // expect...
        Notification::assertSentTo($account, AccountPasswordResetNotification::class);
    }


    public function testPasswordForm_renders()
    {
        // given...
        $reset = $this->createPasswordReset();
        $url   = $reset->getPasswordResetUrl();

        // when...
        $response = $this->get($url);

        // expect...
        $response->assertStatus(200);
    }
}
