<?php

use App\Models\Account;

beforeEach(function () {
    config(['audit.console' => true]);
});

it('records account activation as a manual audit event', function () {
    $actor = $this->adminAccount();
    $account = Account::factory()->create(['activated' => false]);

    $this->actingAs($actor)
        ->post('/manage/accounts/'.$account->id.'/activate')
        ->assertRedirect(route('manage.accounts.show', $account));

    $audit = $account->audits()->where('event', 'manually activated')->firstOrFail();

    expect($account->fresh()->activated)->toBeTrue();
    expect($audit->user_id)->toBe($actor->getAuthIdentifier());
    expect($audit->old_values)->toBe(['activated' => false]);
    expect($audit->new_values)->toBe(['activated' => true]);
    expect($account->audits()->where('event', 'updated')->exists())->toBeFalse();
});

it('records account deactivation as a manual audit event', function () {
    $actor = $this->adminAccount();
    $account = Account::factory()->create(['activated' => true]);

    $this->actingAs($actor)
        ->delete('/manage/accounts/'.$account->id.'/activate')
        ->assertRedirect(route('manage.accounts.show', $account));

    $audit = $account->audits()->where('event', 'manually deactivated')->firstOrFail();

    expect($account->fresh()->activated)->toBeFalse();
    expect($audit->user_id)->toBe($actor->getAuthIdentifier());
    expect($audit->old_values)->toBe(['activated' => true]);
    expect($audit->new_values)->toBe(['activated' => false]);
    expect($account->audits()->where('event', 'updated')->exists())->toBeFalse();
});
