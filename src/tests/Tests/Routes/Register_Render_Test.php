<?php
namespace Tests\Integration;

use Entities\Accounts\Models\UnactivatedAccount;
use Entities\Accounts\Notifications\AccountActivationNotification;
use Domains\Library\Recaptcha\RecaptchaRule;
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

    protected function setUp(): void
    {
        parent::setUp();

        // prevent real emails being sent
        Notification::fake();

        // disable recaptcha in registration tests
        RecaptchaRule::enable(false);
    }


    public function testDoesRender()
    {
        $response = $this->get('register');
        $response->assertStatus(200);
    }

}
