<?php

namespace Tests\Unit\App\Models;

use App\Models\Account;
use App\Models\Role;

it('identifies administrator accounts', function () {
    $account = Account::factory()->create();
    $account->roles()->attach(Role::factory()->administrator()->create());

    $this->assertTrue($account->isAdmin());
    $this->assertFalse(Account::factory()->create()->isAdmin());
});

it('identifies staff accounts', function () {
    $account = Account::factory()->create();
    $account->roles()->attach(Role::factory()->staff()->create());

    $this->assertTrue($account->isStaff());
    $this->assertFalse(Account::factory()->create()->isStaff());
});

it('preserves the auth identifier and releases only scoped OIDC claims', function () {
    config()->set('oidc-server.scopes', [
        'openid' => ['claims' => ['sub']],
        'profile' => ['claims' => ['name', 'nickname', 'preferred_username', 'picture']],
        'email' => ['claims' => ['email', 'email_verified']],
    ]);

    $account = new Account;
    $account->setAttribute($account->getAuthIdentifierName(), 1234);
    $account->email = 'player@example.com';
    $account->username = 'ExamplePlayer';

    $claims = $account->getOidcClaims(['openid', 'profile', 'email']);

    $this->assertSame('1234', $account->getOidcSubject());
    $this->assertSame([
        'sub' => '1234',
        'name' => 'ExamplePlayer',
        'nickname' => 'ExamplePlayer',
        'preferred_username' => 'ExamplePlayer',
        'email' => 'player@example.com',
    ], $claims);
    $this->assertArrayNotHasKey('picture', $claims);
    $this->assertArrayNotHasKey('email_verified', $claims);

    $account->email = 'updated@example.com';
    $account->username = 'RenamedPlayer';

    $this->assertSame('1234', $account->getOidcSubject());
});
