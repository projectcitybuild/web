<?php

use App\Models\ServerToken;

beforeEach(function () {
    config(['audit.console' => true]);
});

it('redacts server tokens in audit values', function () {
    $serverToken = ServerToken::factory()->create([
        'token' => 'sensitive-server-token',
    ]);

    $createdAudit = $serverToken->audits()->where('event', 'created')->firstOrFail();

    expect($createdAudit->new_values['token'])->toBe('[REDACTED]');
    expect($createdAudit->new_values)->not->toContain('sensitive-server-token');

    $serverToken->update(['token' => 'rotated-sensitive-server-token']);
    $updatedAudit = $serverToken->audits()->where('event', 'updated')->firstOrFail();

    expect($updatedAudit->old_values['token'])->toBe('[REDACTED]');
    expect($updatedAudit->new_values['token'])->toBe('[REDACTED]');
    expect($updatedAudit->old_values)->not->toContain('sensitive-server-token');
    expect($updatedAudit->new_values)->not->toContain('rotated-sensitive-server-token');
});
