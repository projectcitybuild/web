<?php

use App\Domains\Donations\Notifications\DonationEndedNotification;
use App\Domains\Donations\UseCases\ExpireDonorPerks;
use App\Models\Account;
use App\Models\Donation;
use App\Models\DonationPerk;
use App\Models\Role;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    $this->donorRole = Role::factory()->donor()->create();
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

it('removes user from donor role on expiry', function () {
    $this->account->roles()->attach($this->donorRole);

    DonationPerk::factory()
        ->for($this->account)
        ->for(Donation::factory()->for($this->account))
        ->expired()
        ->create();

    $this->assertDatabaseHas('roles_accounts', [
        'account_id' => $this->account->getKey(),
        'role_id' => $this->donorRole->getKey(),
    ]);

    $this->useCase->execute();

    $this->assertDatabaseMissing('roles_accounts', [
        'account_id' => $this->account->getKey(),
        'role_id' => $this->donorRole->getKey(),
    ]);
});

it('does not remove donor role if user has another active perk', function () {
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

    $this->account->roles()->attach($this->donorRole);

    $this->assertDatabaseHas('roles_accounts', [
        'account_id' => $this->account->getKey(),
        'role_id' => $this->donorRole->getKey(),
    ]);

    $this->useCase->execute();

    $this->assertDatabaseHas('roles_accounts', [
        'account_id' => $this->account->getKey(),
        'role_id' => $this->donorRole->getKey(),
    ]);
});
