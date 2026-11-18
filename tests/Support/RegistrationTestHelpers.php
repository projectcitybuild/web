<?php

namespace Tests\Support;

use App\Domains\Captcha\Validator\CaptchaValidator;
use App\Models\Account;
use Mockery\MockInterface;
use Tests\TestCase;

/** @mixin TestCase */
trait RegistrationTestHelpers
{
    protected function createUnactivatedAccount(array $attributes = []): Account
    {
        return Account::factory()
            ->unactivated()
            ->make($attributes);
    }

    /** @return array<string, mixed> */
    protected function validFormDataFor(Account $account): array
    {
        return array_merge($account->toArray(), [
            'password' => 'password123456',
            'captcha-response' => 'captcha',
            'terms' => 1,
        ]);
    }

    protected function mockCaptcha(bool $passed = true): void
    {
        $this->mock(CaptchaValidator::class, function (MockInterface $mock) use ($passed): void {
            $mock->shouldReceive('passed')
                ->once()
                ->andReturn($passed);
        });
    }
}
