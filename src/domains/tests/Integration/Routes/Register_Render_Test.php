<?php
namespace Tests\Integration;

use Application\Modules\Accounts\Models\UnactivatedAccount;
use Application\Modules\Accounts\Notifications\AccountActivationNotification;
use Infrastructure\Library\Recaptcha\RecaptchaRule;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class Register_Render_Test extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;

    private $validRegistrationInput = [
        'email'                 => 'test_user@projectcitybuild.com',
        'password'              => 'test1234',
        'password_confirm'      => 'test1234',
        'g-recaptcha-response'  => 'test',
    ];

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
        $response = $this->get('register');
        $response->assertStatus(200);
    }

    public function testRegister_acceptsValidInput()
    {
        // given...
        $data = $this->validRegistrationInput;

        // when...
        $response = $this->post('register', $data);

        // expect...
        $response->assertStatus(200);
        $response->assertViewIs('register-success');
    }

    public function testRegister_badInputRedirectsBack()
    {
        // given...
        $data = ['email' => 'bad_input'];
        $this->get('register');

        // when...
        $response = $this->post('register', $data);

        // expect...
        $response->assertRedirect('register');
        $response->assertSessionHasErrors(['email']);
    }

    public function testRegister_createsUnactivatedAccount()
    {
        // given...
        $data = $this->validRegistrationInput;

        // when...
        $this->post('register', $data);

        // expect...
        $account = UnactivatedAccount::where('email', $data['email'])->first();
        $this->assertNotNull($account);
    }

    public function testRegister_sendsActivationMail()
    {
        // given...
        $data = $this->validRegistrationInput;

        // when...
        $this->post('register', $data);

        // expect...
        $account = UnactivatedAccount::where('email', $data['email'])->first();
        $this->assertNotNull($account);

        Notification::assertSentTo($account, AccountActivationNotification::class);
    }
}
