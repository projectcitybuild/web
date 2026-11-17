<?php

use App\Models\Account;
use App\Models\Server;

beforeEach(function () {
    config(['audit.console' => true]);
});

it('audits only explicitly included server attributes when created', function () {
    $server = Server::factory()->create([
        'name' => 'Test Server',
        'ip' => '192.168.1.1',
        'web_port' => 8080,
    ]);

    $audit = $server->audits()->where('event', 'created')->firstOrFail();

    expect($audit->new_values)->toMatchArray([
        'name' => 'Test Server',
        'ip' => '192.168.1.1',
    ]);
    expect($audit->new_values)->not->toHaveKey('web_port');
});

it('records only included values when included and excluded server attributes change together', function () {
    $server = Server::factory()->create([
        'name' => 'Old Name',
        'web_port' => 8080,
    ]);

    $server->update([
        'name' => 'New Name',
        'web_port' => 8081,
    ]);

    $audit = $server->audits()->where('event', 'updated')->firstOrFail();

    expect($audit->old_values)->toMatchArray(['name' => 'Old Name']);
    expect($audit->new_values)->toMatchArray(['name' => 'New Name']);
    expect($audit->new_values)->not->toHaveKey('web_port');
});

it('does not create an audit when only excluded server attributes change', function () {
    $server = Server::factory()->create(['web_port' => 8080]);
    $auditCount = $server->audits()->count();

    $server->update(['web_port' => 8081]);

    expect($server->audits()->count())->toBe($auditCount);
});

it('attributes a server audit to the authenticated account', function () {
    $actor = Account::factory()->create();
    $this->actingAs($actor);

    $server = Server::factory()->create();

    $audit = $server->audits()->where('event', 'created')->firstOrFail();

    expect($audit->user_id)->toBe($actor->getAuthIdentifier());
    expect($audit->user_type)->toBe($actor->getMorphClass());
});
