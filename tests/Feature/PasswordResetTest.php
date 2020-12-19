<?php


namespace Tests\Feature;


use App\Entities\Accounts\Models\Account;
use App\Entities\Accounts\Models\AccountPasswordReset;
use App\Entities\Accounts\Notifications\AccountPasswordResetNotification;
use App\Http\Actions\AccountPasswordReset\SendPasswordResetEmail;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    private $account;

    /**
     * @var SendPasswordResetEmail
     */
    private $sendPasswordResetEmail;

    /**
     * @var AccountPasswordReset|\Illuminate\Database\Eloquent\Builder
     */
    private $passwordReset;

    protected function setUp(): void
    {
        parent::setUp();
        $this->account = factory(Account::class)->create();
        $this->sendPasswordResetEmail = new SendPasswordResetEmail();

        $this->sendPasswordResetEmail->execute($this->account, $this->account->email);

        $this->passwordReset = AccountPasswordReset::whereEmail($this->account->email);
    }

    public function testUserCanRequestPasswordResetEmail()
    {
        Notification::fake();
        Notification::assertNothingSent();

        $this->post(route('front.password-reset.store'), [
            'email' => $this->account->email
        ])->assertSessionHasNoErrors();

        Notification::assertSentTo(
            [$this->account], AccountPasswordResetNotification::class
        );
    }

    public function testUserCanViewPasswordReset()
    {
        $reset = factory(AccountPasswordReset::class)->create();

        $this->get($reset->getPasswordResetUrl())
        ->assertSuccessful();
    }

    public function testUserCanChangePassword()
    {
        $reset = factory(AccountPasswordReset::class)->create();

        $this->get($reset->getPasswordResetUrl())
            ->assertSuccessful();
    }
}
