<?php

namespace Tests\Feature;

use App\Entities\Accounts\Models\Account;
use App\Entities\Accounts\Models\AccountPasswordReset;
use App\Entities\Accounts\Notifications\AccountPasswordResetNotification;
use App\Http\Actions\AccountSettings\UpdateAccountPassword;
use Domain\PasswordReset\PasswordResetService;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    private $account;

    private PasswordResetService $passwordResetService;

    /**
     * @var AccountPasswordReset|\Illuminate\Database\Eloquent\Builder
     */
    private $passwordReset;

    protected function setUp(): void
    {
        parent::setUp();
        $this->account = Account::factory()->create();
        $this->passwordResetService = new PasswordResetService(new UpdateAccountPassword());

        $this->passwordResetService->sendPasswordResetEmail($this->account, $this->account->email);

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
