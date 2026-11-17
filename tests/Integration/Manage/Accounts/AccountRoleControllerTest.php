<?php

use App\Models\Account;
use App\Models\Role;

beforeEach(function () {
    config(['audit.console' => true]);
});

it('audits account role changes through the manage endpoint', function () {
    $actor = $this->adminAccount();
    $account = Account::factory()->create();
    $role = Role::factory()->create(['name' => 'audited-role']);

    $this->actingAs($actor)
        ->put('/manage/accounts/'.$account->id.'/roles', [
            'account_role_ids' => [$role->id],
        ])
        ->assertRedirect(route('manage.accounts.show', $account));

    $audit = $account->audits()->where('event', 'sync')->firstOrFail();

    expect($account->roles()->whereKey($role->id)->exists())->toBeTrue();
    expect($audit->user_id)->toBe($actor->getAuthIdentifier());
    expect($audit->new_values)->toBe(['role_names' => ['audited-role']]);
});
