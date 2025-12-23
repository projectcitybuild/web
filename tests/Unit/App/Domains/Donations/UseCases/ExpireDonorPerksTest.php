<?php

use App\Domains\Donations\Notifications\DonationEndedNotification;
use App\Domains\Donations\UseCases\ExpireDonorPerks;
use App\Models\Account;
use App\Models\Donation;
use App\Models\DonationPerk;
use App\Models\Group;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    $this->donorGroup = Group::factory()->donor()->create();
    $this->account = Account::factory()->create();
    $this->useCase = new ExpireDonorPerks;

    Notification::fake();
});

it('does not deactivate perk if not expired', function () {
    $perk = DonationPerk::factory()
        ->for($this->account)
        ->for(Donation::factory()->for($this->account))
        ->notExpired()
        ->create();

    $this->useCase->execute();

    $perk->refresh();
    expect($perk->is_active)->toBeTrue();
});

it('grants grace period of 12 hours', function () {
    $this->freezeTime(function (Carbon $now) {
        $perk = DonationPerk::factory()
            ->for($this->account)
            ->for(Donation::factory()->for($this->account))
            ->create(['expires_at' => $now->subMinute()]);

        $this->useCase->execute();

        $perk->refresh();
        expect($perk->is_active)->toBeTrue();
    });
});

it('deactivates after grace period', function () {
    $this->freezeTime(function (Carbon $now) {
        $perk = DonationPerk::factory()
            ->for($this->account)
            ->for(Donation::factory()->for($this->account))
            ->create(['expires_at' => $now->subHours(12)->subMinute()]);

        $this->useCase->execute();

        $perk->refresh();
        expect($perk->is_active)->toBeFalse();
    });
});

it('sends email to user when perks expires', function () {
    DonationPerk::factory()
        ->for($this->account)
        ->for(Donation::factory()->for($this->account))
        ->expired()
        ->create();

    $this->useCase->execute();

    Notification::assertSentTo($this->account, DonationEndedNotification::class);
});

it('does not send email if user has another active perk', function () {
    DonationPerk::factory()
        ->for($this->account)
        ->for(Donation::factory()->for($this->account))
        ->expired()
        ->create();

    DonationPerk::factory()
        ->for($this->account)
        ->for(Donation::factory()->for($this->account))
        ->notExpired()
        ->create();

    $this->useCase->execute();

    Notification::assertNothingSentTo($this->account);
});

it('removes user from donor group on expiry', function () {
    $this->account->groups()->attach($this->donorGroup);

    DonationPerk::factory()
        ->for($this->account)
        ->for(Donation::factory()->for($this->account))
        ->expired()
        ->create();

    $this->assertDatabaseHas('groups_accounts', [
        'account_id' => $this->account->getKey(),
        'group_id' => $this->donorGroup->getKey(),
    ]);

    $this->useCase->execute();

    $this->assertDatabaseMissing('groups_accounts', [
        'account_id' => $this->account->getKey(),
        'group_id' => $this->donorGroup->getKey(),
    ]);
});

it('does not remove donor group if user has another active perk', function () {
    DonationPerk::factory()
        ->for($this->account)
        ->for(Donation::factory()->for($this->account))
        ->expired()
        ->create();

    DonationPerk::factory()
        ->for($this->account)
        ->for(Donation::factory()->for($this->account))
        ->notExpired()
        ->create();

    $this->account->groups()->attach($this->donorGroup);

    $this->assertDatabaseHas('groups_accounts', [
        'account_id' => $this->account->getKey(),
        'group_id' => $this->donorGroup->getKey(),
    ]);

    $this->useCase->execute();

    $this->assertDatabaseHas('groups_accounts', [
        'account_id' => $this->account->getKey(),
        'group_id' => $this->donorGroup->getKey(),
    ]);
});
