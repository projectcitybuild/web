<?php

namespace Tests\Unit\Domain\PasswordReset\UseCases;

use App\Domains\PasswordReset\UseCases\DeleteExpiredPasswordResets;
use App\Models\AccountPasswordReset;
use Illuminate\Support\Carbon;
use Repositories\AccountPasswordResetRepository;
use Tests\TestCase;

class DeleteExpiredPasswordResetsTest extends TestCase
{
    private DeleteExpiredPasswordResets $useCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->useCase = new DeleteExpiredPasswordResets(
            passwordResetRepository: new AccountPasswordResetRepository(),
        );
    }

    public function test_deletes_expired_resets()
    {
        $now = Carbon::create(year: 2022, month: 4, day: 8, hour: 10, minute: 9, second: 8);
        Carbon::setTestNow($now);

        AccountPasswordReset::factory()->create([
            'created_at' => $now->copy()->addDays(-2),
        ]);
        AccountPasswordReset::factory()->create([
            'created_at' => $now->copy()->addDays(-1),
        ]);
        AccountPasswordReset::factory()->create([
            'created_at' => $now,
        ]);
        AccountPasswordReset::factory()->create([
            'created_at' => $now->copy()->addDay(),
        ]);

        $this->assertDatabaseCount(
            table: 'account_password_resets',
            count: 4,
        );

        $this->useCase->execute();

        $this->assertDatabaseCount(
            table: 'account_password_resets',
            count: 2,
        );
        $this->assertDatabaseHas(
            table: 'account_password_resets',
            data: ['created_at' => $now],
        );
        $this->assertDatabaseHas(
            table: 'account_password_resets',
            data: ['created_at' => $now->copy()->addDay()],
        );
    }
}
