<?php
namespace Tests\Integration;

use App\Modules\Accounts\Models\AccountPasswordReset;
use App\Modules\Accounts\Services\PasswordResetCleanupService;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PasswordResetCleanupService_Test extends TestCase {
    use RefreshDatabase;

    private function makeRecord(string $email, int $daysBefore) : AccountPasswordReset {
        return AccountPasswordReset::create([
            'email'      => $email,
            'token'      => 'test',
            'created_at' => now()->subDays($daysBefore),
        ]);
    }

    function testDeletesOldRecords() {
        // given...
        $service = resolve(PasswordResetCleanupService::class);
        $this->makeRecord('test@projectcitybuild.com', $service::DAY_THRESHOLD + 1);

        // when...
        $service->cleanup();

        // expect...
        $this->assertEquals(0, AccountPasswordReset::count());
    }

    function testDeletesOnlyOldRecords() {
        // given...
        $service = resolve(PasswordResetCleanupService::class);
        $this->makeRecord('deleted_email', $service::DAY_THRESHOLD + 1);
        $this->makeRecord('undeleted_email', 3);
        $this->assertEquals(2, AccountPasswordReset::count());

        // when...
        $service->cleanup();

        // expect...
        $this->assertEquals(1, AccountPasswordReset::count());
        $this->assertEquals('undeleted_email', AccountPasswordReset::first()->email);
    }

}