<?php

namespace Domain\PasswordReset\UseCases;

use App\Core\Domains\SignedURL\Adapters\LaravelSignedURLGenerator;
use App\Core\Domains\Tokens\Adapters\StubTokenGenerator;
use App\Domains\PasswordReset\UseCases\SendPasswordResetEmail;
use App\Models\Account;
use App\Models\AccountPasswordReset;
use Entities\Notifications\AccountPasswordResetNotification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use Repositories\AccountPasswordResetRepository;
use Tests\TestCase;

class SendPasswordResetEmailTest extends TestCase
{
    private Carbon $now;
    private Account $account;
    private AccountPasswordResetRepository $passwordResetRepository;
    private SendPasswordResetEmail $useCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->account = Account::factory()->create();
        $this->passwordResetRepository = \Mockery::mock(AccountPasswordResetRepository::class)->makePartial();

        $this->useCase = new SendPasswordResetEmail(
            passwordResetRepository: $this->passwordResetRepository,
            tokenGenerator: new StubTokenGenerator('token'),
            signedURLGenerator: new LaravelSignedURLGenerator()
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
