<?php

use App\Core\Domains\Discord\Jobs\SyncDiscordRoleJob;
use App\Domains\Permissions\WebManagePermission;
use App\Models\Account;
use App\Models\ExternalAccount;
use Illuminate\Support\Facades\Bus;
use Tests\Support\StaffAccountFactory;

uses(StaffAccountFactory::class);

it('queues a role sync for a linked Discord account', function () {
    $manager = $this->createStaffAccountWithPermissions([WebManagePermission::ACCOUNTS_EDIT->value]);
    $account = Account::factory()->create();
    $externalAccount = ExternalAccount::factory()->create([
        'account_id' => $account->id,
        'provider_user_id' => '123456789012345678',
    ]);
    Bus::fake([SyncDiscordRoleJob::class]);

    $this->actingAs($manager)
        ->post(route('manage.accounts.discord.sync', $account))
        ->assertRedirect(route('manage.accounts.show', $account))
        ->assertSessionHas('success', 'A Discord role sync has been queued.');

    Bus::assertDispatched(
        SyncDiscordRoleJob::class,
        fn (SyncDiscordRoleJob $job): bool => $job->discordUserId === $externalAccount->provider_user_id,
    );
});

it('does not queue a role sync when no Discord account is linked', function () {
    $manager = $this->createStaffAccountWithPermissions([WebManagePermission::ACCOUNTS_EDIT->value]);
    $account = Account::factory()->create();
    Bus::fake([SyncDiscordRoleJob::class]);

    $this->actingAs($manager)
        ->post(route('manage.accounts.discord.sync', $account))
        ->assertRedirect(route('manage.accounts.show', $account))
        ->assertSessionHas('error', 'Link a Discord account before requesting a role sync.');

    Bus::assertNotDispatched(SyncDiscordRoleJob::class);
});

it('forbids forcing a Discord role sync without account edit permission', function () {
    $manager = $this->createStaffAccountWithPermissions([WebManagePermission::ACCOUNTS_VIEW->value]);
    $account = Account::factory()->create();
    ExternalAccount::factory()->create(['account_id' => $account->id]);
    Bus::fake([SyncDiscordRoleJob::class]);

    $this->actingAs($manager)
        ->post(route('manage.accounts.discord.sync', $account))
        ->assertForbidden();

    Bus::assertNotDispatched(SyncDiscordRoleJob::class);
});

it('force disconnects a Discord account and queues role cleanup', function () {
    config(['audit.console' => true]);
    $manager = $this->createStaffAccountWithPermissions([WebManagePermission::ACCOUNTS_EDIT->value]);
    $account = Account::factory()->create();
    $externalAccount = ExternalAccount::factory()->create([
        'account_id' => $account->id,
        'provider_user_id' => '123456789012345678',
        'username' => 'builder',
    ]);
    Bus::fake([SyncDiscordRoleJob::class]);

    $this->actingAs($manager)
        ->delete(route('manage.accounts.discord.destroy', $account))
        ->assertRedirect(route('manage.accounts.show', $account))
        ->assertSessionHas('success', 'Discord account disconnected successfully.');

    $this->assertDatabaseMissing('external_accounts', ['id' => $externalAccount->id]);
    $audit = $account->audits()->where('event', 'disconnected Discord account')->firstOrFail();
    expect($audit->old_values)->toBe([
        'discord_user_id' => $externalAccount->provider_user_id,
        'discord_username' => $externalAccount->username,
    ]);
    Bus::assertDispatched(
        SyncDiscordRoleJob::class,
        fn (SyncDiscordRoleJob $job): bool => $job->discordUserId === $externalAccount->provider_user_id,
    );
});

it('forbids disconnecting a Discord account without account edit permission', function () {
    $manager = $this->createStaffAccountWithPermissions([WebManagePermission::ACCOUNTS_VIEW->value]);
    $account = Account::factory()->create();
    $externalAccount = ExternalAccount::factory()->create(['account_id' => $account->id]);
    Bus::fake([SyncDiscordRoleJob::class]);

    $this->actingAs($manager)
        ->delete(route('manage.accounts.discord.destroy', $account))
        ->assertForbidden();

    $this->assertModelExists($externalAccount);
    Bus::assertNotDispatched(SyncDiscordRoleJob::class);
});
