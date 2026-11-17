<?php

use App\Models\Account;
use App\Models\Role;

beforeEach(function () {
    config(['audit.console' => true]);
});

it('audits only explicitly included account attributes', function () {
    $account = Account::factory()->create([
        'email' => 'audited@example.test',
        'username' => 'audited-account',
    ]);

    $audit = $account->audits()->where('event', 'created')->firstOrFail();

    expect($audit->new_values)->toMatchArray([
        'email' => 'audited@example.test',
        'username' => 'audited-account',
    ]);
    expect($audit->new_values)->not->toHaveKey('password');
    expect($audit->new_values)->not->toHaveKey('remember_token');
});

it('records role sync changes as role names', function () {
    $account = Account::factory()->create();
    $oldRole = Role::factory()->create(['name' => 'old-role']);
    $newRole = Role::factory()->create(['name' => 'new-role']);
    $account->roles()->attach($oldRole);

    $account->auditSync('roles', [$newRole->id], true, ['roles.id', 'roles.name']);
    $audit = $account->audits()->where('event', 'sync')->firstOrFail();

    expect($audit->old_values)->toBe(['role_names' => ['old-role']]);
    expect($audit->new_values)->toBe(['role_names' => ['new-role']]);
});
