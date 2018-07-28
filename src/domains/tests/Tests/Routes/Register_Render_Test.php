<?php
namespace Tests\Integration;

use Domains\Modules\Accounts\Models\UnactivatedAccount;
use Domains\Modules\Accounts\Notifications\AccountActivationNotification;
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

}
