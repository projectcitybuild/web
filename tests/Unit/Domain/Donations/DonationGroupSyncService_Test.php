<?php

namespace Tests\Unit\Domain\Donations;

use App\Entities\Accounts\Models\Account;
use App\Entities\Groups\Models\Group;
use App\Http\Actions\SyncUserToDiscourse;
use Domain\Donations\DonationGroupSyncService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class DonationGroupSyncService_Test extends TestCase
{
    use RefreshDatabase;

    private $discourseSyncMock;
    private int $donorGroupId;
    private int $memberGroupId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->donorGroupId = Group::factory()->donor()->create()->getKey();
        $this->memberGroupId = Group::factory()->member()->create()->getKey();
        $this->discourseSyncMock = $this->mock(SyncUserToDiscourse::class);
    }

    public function testAdd_addsDonorGroup()
    {
        $account = Account::factory()->create();

        $this->assertFalse($account->groups->contains($this->donorGroupId));

        $this->discourseSyncMock->shouldIgnoreMissing();
        $service = new DonationGroupSyncService($this->discourseSyncMock);
        $service->addToDonorGroup($account);

        $this->assertTrue(Account::find($account->getKey())
            ->groups
            ->contains($this->donorGroupId)
        );
    }

    public function testAdd_removesMemberGroup()
    {
        $account = Account::factory()->create();
        $account->groups()->attach($this->memberGroupId);

        $this->assertTrue($account->groups->contains($this->memberGroupId));

        $this->discourseSyncMock->shouldIgnoreMissing();
        $service = new DonationGroupSyncService($this->discourseSyncMock);
        $service->addToDonorGroup($account);

        $this->assertFalse(Account::find($account->getKey())
            ->groups
            ->contains($this->memberGroupId)
        );
    }

    public function testAdd_syncsWithDiscourse()
    {
        $account = Account::factory()->create();

        $discourseSyncService = $this->spy(SyncUserToDiscourse::class);
        $service = new DonationGroupSyncService($discourseSyncService);
        $service->addToDonorGroup($account);

        $discourseSyncService->shouldHaveReceived('syncAll');
    }

    public function testRemove_removesDonorGroup()
    {
        $account = Account::factory()->create();
        $account->groups()->attach($this->donorGroupId);

        $this->assertTrue($account->groups->contains($this->donorGroupId));

        $this->discourseSyncMock->shouldIgnoreMissing();
        $service = new DonationGroupSyncService($this->discourseSyncMock);
        $service->removeFromDonorGroup($account);

        $this->assertFalse(Account::find($account->getKey())
            ->groups
            ->contains($this->donorGroupId)
        );
    }

    public function testRemove_addsMemberGroupIfNoOtherGroup()
    {
        $account = Account::factory()->create();
        $account->groups()->attach($this->donorGroupId);

        $this->discourseSyncMock->shouldIgnoreMissing();
        $service = new DonationGroupSyncService($this->discourseSyncMock);
        $service->removeFromDonorGroup($account);

        $this->assertTrue(Account::find($account->getKey())
            ->groups
            ->contains($this->memberGroupId)
        );
    }

    public function testRemove_doesNotAddMemberGroupIfOtherGroup()
    {
        $group = Group::factory()->create();
        $account = Account::factory()->create();
        $account->groups()->attach($this->donorGroupId);
        $account->groups()->attach($group->getKey());

        $this->discourseSyncMock->shouldIgnoreMissing();
        $service = new DonationGroupSyncService($this->discourseSyncMock);
        $service->removeFromDonorGroup($account);

        $this->assertFalse(Account::find($account->getKey())
            ->groups
            ->contains($this->memberGroupId)
        );
    }

    public function testRemove_syncsWithDiscourse()
    {
        $account = Account::factory()->create();
        $account->groups()->attach($this->donorGroupId);

        $discourseSyncService = $this->spy(SyncUserToDiscourse::class);
        $service = new DonationGroupSyncService($discourseSyncService);
        $service->removeFromDonorGroup($account);

        $discourseSyncService->shouldHaveReceived('syncAll');
    }
}
