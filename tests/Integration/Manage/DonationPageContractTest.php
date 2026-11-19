<?php

use App\Domains\Permissions\WebManagePermission;
use App\Models\Account;
use App\Models\Donation;
use App\Models\Payment;
use Illuminate\Testing\Fluent\AssertableJson as Assert;
use Tests\Support\StaffAccountFactory;

uses(StaffAccountFactory::class);

it('serializes donation list and edit pages through the donation DTO', function () {
    config(['inertia.ssr.enabled' => false]);

    $manager = $this->createStaffAccountWithPermissions([
        WebManagePermission::DONATIONS_VIEW->value,
        WebManagePermission::DONATIONS_EDIT->value,
    ]);
    $account = Account::factory()->create([
        'username' => 'contract-donor',
        'email' => 'private-donor@example.test',
    ]);
    $payment = Payment::factory()->create([
        'original_currency' => 'aud',
        'original_unit_amount' => 2500,
        'unit_quantity' => 2,
    ]);
    $donation = Donation::factory()->create([
        'account_id' => $account->id,
        'payment_id' => $payment->id,
        'amount' => 50,
    ]);

    $this->actingAs($manager)
        ->get(route('manage.donations.edit', $donation))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Donations/DonationEdit')
            ->where('donation.id', $donation->id)
            ->where('donation.amount', 50)
        );

    $version = $this->actingAs($manager)
        ->withHeader('X-Inertia', 'true')
        ->get(route('manage.donations.index'))
        ->headers->get('X-Inertia-Version');

    $this->withHeaders([
        'X-Inertia' => 'true',
        'X-Inertia-Version' => $version,
        'X-Inertia-Partial-Component' => 'Donations/DonationList',
        'X-Inertia-Partial-Data' => 'donations',
    ])
        ->get(route('manage.donations.index'))
        ->assertOk()
        ->assertJsonPath('props.donations.data.0.id', $donation->id)
        ->assertJsonPath('props.donations.data.0.account.username', 'contract-donor')
        ->assertJsonPath('props.donations.data.0.account.email', 'private-donor@example.test')
        ->assertJsonPath('props.donations.data.0.payment.original_unit_amount', 2500)
        ->assertJsonPath('props.donations.data.0.payment.unit_quantity', 2);
});
