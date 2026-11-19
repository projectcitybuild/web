<?php

use App\Domains\Permissions\WebManagePermission;
use App\Models\Account;
use App\Models\Donation;
use App\Models\DonationPerk;
use Illuminate\Testing\Fluent\AssertableJson as Assert;
use Tests\Support\StaffAccountFactory;

uses(StaffAccountFactory::class);

it('lists donation perks with filtering and sort state for infinite pagination', function () {
    config(['inertia.ssr.enabled' => false]);

    $manager = $this->createStaffAccountWithPermissions([
        WebManagePermission::DONATIONS_VIEW->value,
    ]);
    $account = Account::factory()->create(['username' => 'perk-search-donor']);
    $donation = Donation::factory()->create(['account_id' => $account->id]);
    $perk = DonationPerk::factory()->create([
        'account_id' => $account->id,
        'donation_id' => $donation->id,
        'is_active' => true,
        'expires_at' => now()->addDay(),
    ]);
    DonationPerk::factory()->count(50)->create([
        'account_id' => $account->id,
        'donation_id' => $donation->id,
        'is_active' => true,
        'expires_at' => now()->addDay(),
    ]);

    $filters = [
        'search' => 'perk-search-donor',
        'status' => 'active',
        'sort' => 'account_id',
        'direction' => 'asc',
    ];
    $firstPage = $this->actingAs($manager)
        ->getJson(route('manage.donation-perks.index', $filters))
        ->assertOk()
        ->assertJsonPath('data.0.id', $perk->id)
        ->assertJsonPath('data.0.account_username', 'perk-search-donor')
        ->assertJsonPath('total', 51)
        ->assertJsonPath('current_page', 1)
        ->assertJsonCount(50, 'data');

    expect($firstPage->json('next_page_url'))->not->toBeNull();

    $this->getJson(route('manage.donation-perks.index', [...$filters, 'page' => 2]))
        ->assertOk()
        ->assertJsonPath('current_page', 2)
        ->assertJsonPath('total', 51)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('next_page_url', null);
});

it('creates, updates, and deletes a donation perk for an account', function () {
    $manager = $this->createStaffAccountWithPermissions([
        WebManagePermission::DONATIONS_EDIT->value,
    ]);
    $account = Account::factory()->create();
    $donation = Donation::factory()->create(['account_id' => $account->id]);

    $this->actingAs($manager)
        ->post(route('manage.donation-perks.store'), [
            'account_id' => $account->id,
            'donation_id' => $donation->id,
            'donation_tier_id' => null,
            'is_active' => true,
            'expires_at' => now()->addDay()->toDateTimeString(),
        ])
        ->assertRedirect(route('manage.donation-perks.index'));

    $perk = DonationPerk::query()->latest('id')->firstOrFail();
    expect($perk->account_id)->toBe($account->id)
        ->and($perk->donation_id)->toBe($donation->id);

    $this->put(route('manage.donation-perks.update', $perk), [
        'account_id' => $account->id,
        'donation_id' => $donation->id,
        'donation_tier_id' => null,
        'is_active' => false,
        'expires_at' => null,
    ])->assertRedirect(route('manage.donation-perks.edit', $perk));

    expect($perk->fresh()->is_active)->toBeFalse();

    $this->delete(route('manage.donation-perks.destroy', $perk))
        ->assertRedirect(route('manage.donation-perks.index'));

    $this->assertDatabaseMissing(DonationPerk::tableName(), ['id' => $perk->id]);
});

it('provides create and edit form props and validates related records', function () {
    config(['inertia.ssr.enabled' => false]);

    $manager = $this->createStaffAccountWithPermissions([
        WebManagePermission::DONATIONS_EDIT->value,
    ]);
    $account = Account::factory()->create(['username' => 'perk-edit-account']);
    $donation = Donation::factory()->create(['account_id' => $account->id]);
    $perk = DonationPerk::factory()->create([
        'account_id' => $account->id,
        'donation_id' => $donation->id,
    ]);

    $this->actingAs($manager)
        ->get(route('manage.donation-perks.create'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('DonationPerks/DonationPerkCreate')
            ->has('tiers')
        );

    $this->get(route('manage.donation-perks.edit', $perk))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('DonationPerks/DonationPerkEdit')
            ->where('perk.id', $perk->id)
            ->where('perk.account_username', 'perk-edit-account')
            ->where('perk.donation_id', $donation->id)
        );

    $this->from(route('manage.donation-perks.create'))
        ->post(route('manage.donation-perks.store'), [
            'account_id' => 999999,
            'donation_id' => 999999,
            'donation_tier_id' => null,
            'is_active' => true,
            'expires_at' => null,
        ])
        ->assertSessionHasErrors(['account_id', 'donation_id']);
});

it('does not allow a view-only manager to edit donation perks', function () {
    $manager = $this->createStaffAccountWithPermissions([
        WebManagePermission::DONATIONS_VIEW->value,
    ]);
    $account = Account::factory()->create();
    $donation = Donation::factory()->create(['account_id' => $account->id]);
    $perk = DonationPerk::factory()->create([
        'account_id' => $account->id,
        'donation_id' => $donation->id,
    ]);

    $this->actingAs($manager)
        ->get(route('manage.donation-perks.edit', $perk))
        ->assertForbidden();

    $this->actingAs($manager)
        ->post(route('manage.donation-perks.store'), [])
        ->assertForbidden();
});
