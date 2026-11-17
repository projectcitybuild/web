<?php

use App\Core\Domains\Auditing\AuditEventRecorder;
use App\Models\Account;
use App\Models\Audit;
use App\Models\Server;

beforeEach(function () {
    config(['audit.console' => true]);
});

it('records a manual audit event with its actor and safe changes', function () {
    $actor = Account::factory()->create();
    $subject = Server::factory()->create();

    $this->actingAs($actor);
    $audit = app(AuditEventRecorder::class)->record(
        subject: $subject,
        event: 'manually approved',
        oldValues: ['status' => 'pending'],
        newValues: ['status' => 'approved'],
    );

    expect($audit)->toBeInstanceOf(Audit::class);
    expect($audit->event)->toBe('manually approved');
    expect($audit->auditable_type)->toBe($subject->getMorphClass());
    expect($audit->auditable_id)->toBe($subject->getKey());
    expect($audit->user_id)->toBe($actor->getAuthIdentifier());
    expect($audit->new_values)->toBe(['status' => 'approved']);
});

it('respects disabled auditing configuration', function () {
    config(['audit.enabled' => false]);
    $subject = Server::factory()->create();

    $audit = app(AuditEventRecorder::class)->record(
        subject: $subject,
        event: 'manually approved',
        newValues: ['status' => 'approved'],
    );

    expect($audit)->toBeNull();
    expect(Audit::where('auditable_id', $subject->getKey())->exists())->toBeFalse();
});

it('respects disabled console auditing configuration', function () {
    config(['audit.console' => false]);
    $subject = Server::factory()->create();

    $audit = app(AuditEventRecorder::class)->record(
        subject: $subject,
        event: 'manually approved',
        newValues: ['status' => 'approved'],
    );

    expect($audit)->toBeNull();
    expect(Audit::where('auditable_id', $subject->getKey())->exists())->toBeFalse();
});
