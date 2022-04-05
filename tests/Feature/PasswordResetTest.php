<?php

namespace Tests\Feature;

use App\Entities\Models\Eloquent\Account;
use App\Entities\Models\Eloquent\AccountPasswordReset;
use App\Entities\Notifications\AccountPasswordResetNotification;
use Domain\PasswordReset\UseCases\SendPasswordResetEmail;
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
        $this->account = Account::factory()->create();
        $this->sendPasswordResetEmail = new SendPasswordResetEmail();

        $this->sendPasswordResetEmail->execute($this->account, $this->account->email);

        $this->passwordReset = AccountPasswordReset::whereEmail($this->account->email);
    }

    public function testUserCanRequestPasswordResetEmail()
    {
        Notification::fake();
        Notification::assertNothingSent();

        $this->post(route('front.password-reset.store'), [
            'email' => $this->account->email,
        ])->assertSessionHasNoErrors();

        Notification::assertSentTo(
            [$this->account], AccountPasswordResetNotification::class
        );
    }

    public function testUserCanViewPasswordReset()
    {
        $reset = AccountPasswordReset::factory()->create();

        $this->get($reset->getPasswordResetUrl())
        ->assertSuccessful();
    }

    public function testUserCanChangePassword()
    {
        $reset = AccountPasswordReset::factory()->create();

        $this->get($reset->getPasswordResetUrl())
            ->assertSuccessful();
    }
}
