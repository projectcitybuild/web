<?php
namespace Tests\Integration;

use App\Modules\Accounts\Models\UnactivatedAccount;
use App\Modules\Accounts\Services\UnactivatedAccountCleanupService;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UnactivatedAccountCleanupService_Test extends TestCase {
    use RefreshDatabase;

    private function makeAccount(string $email, int $daysBefore) : UnactivatedAccount {
        return UnactivatedAccount::forceCreate([
            'email'      => $email,
            'password'   => Hash::make('test'),
            'created_at' => now()->subDays($daysBefore),
            'updated_at' => now()->subDays($daysBefore),
        ]);
    }

    function testDeletesOldRecords() {
        // given...
        $service = resolve(UnactivatedAccountCleanupService::class);
        $this->makeAccount('test@projectcitybuild.com', $service::DAY_THRESHOLD + 1);

        // when...
        $service->cleanup();

        // expect...
        $this->assertEquals(0, UnactivatedAccount::count());
    }

    function testDeletesOnlyOldRecords() {
        // given...
        $service = resolve(UnactivatedAccountCleanupService::class);
        $this->makeAccount('deleted_email', $service::DAY_THRESHOLD + 1);
        $this->makeAccount('undeleted_email', 3);
        $this->assertEquals(2, UnactivatedAccount::count());

        // when...
        $service->cleanup();

        // expect...
        $this->assertEquals(1, UnactivatedAccount::count());
        $this->assertEquals('undeleted_email', UnactivatedAccount::first()->email);
    }

}