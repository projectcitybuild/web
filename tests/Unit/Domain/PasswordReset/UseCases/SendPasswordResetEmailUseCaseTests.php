<?php

namespace Tests\Feature;

use App\Entities\Models\Eloquent\Account;
use App\Entities\Models\Eloquent\AccountPasswordReset;
use App\Entities\Notifications\AccountPasswordResetNotification;
use Domain\PasswordReset\PasswordResetURLGenerator;
use Domain\PasswordReset\Repositories\AccountPasswordResetRepository;
use Domain\PasswordReset\UseCases\SendPasswordResetEmailUseCase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use Library\Tokens\Adapters\StubTokenGenerator;
use Library\Tokens\TokenGenerator;
use Tests\TestCase;

class SendPasswordResetEmailUseCaseTests extends TestCase
{
    private Carbon $now;
    private Account $account;
    private AccountPasswordResetRepository $passwordResetRepository;
    private SendPasswordResetEmailUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->account = Account::factory()->create();
        $this->passwordResetRepository = \Mockery::mock(AccountPasswordResetRepository::class)->makePartial();

        $this->useCase = new SendPasswordResetEmailUseCase(
            passwordResetRepository: $this->passwordResetRepository,
            tokenGenerator: new StubTokenGenerator('token'),
            passwordResetURLGenerator: new PasswordResetURLGenerator(),
        );

        $this->now = Carbon::create(year: 2022, month: 4, day: 6, hour: 10, minute: 9, second: 8);
        Carbon::setTestNow($this->now);

        Notification::fake();
    }

    public function test_sends_notification()
    {
        Notification::assertNothingSent();

        $this->useCase->execute(
            account: $this->account,
            email: $this->account->email
        );

        Notification::assertSentTo($this->account, AccountPasswordResetNotification::class);
    }

    public function test_creates_request()
    {
        $this->useCase->execute(
            account: $this->account,
            email: $this->account->email
        );

        $this->assertDatabaseHas(
            table: 'account_password_resets',
            data: [
                'token' => 'token',
                'email' => $this->account->email,
                'created_at' => $this->now,
            ],
        );
    }

    public function test_updates_existing_request()
    {
        AccountPasswordReset::create([
            'email' => $this->account->email,
            'token' => 'old_token',
            'created_at' => Carbon::create(year: 2021, month: 3, day: 2, hour: 10, minute: 9, second: 8),
        ]);

        $this->useCase->execute(
            account: $this->account,
            email: $this->account->email
        );

        $this->assertDatabaseHas(
            table: 'account_password_resets',
            data: [
                'token' => 'token',
                'email' => $this->account->email,
                'created_at' => $this->now,
            ],
        );
        $this->assertDatabaseCount(
            table: 'account_password_resets',
            count: 1,
        );
    }
}
