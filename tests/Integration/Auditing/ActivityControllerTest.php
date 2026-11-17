<?php

use App\Models\Account;
use App\Models\Audit;
use App\Models\Server;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    config(['audit.console' => true]);
});

it('renders audit records in the auditing application', function () {
    $this->actingAs($this->adminAccount());
    $server = Server::factory()->create(['name' => 'Audited Server']);
    $audit = $server->audits()->where('event', 'created')->firstOrFail();

    $this->get(route('auditing.index', [
        'auditable_type' => $audit->auditable_type,
        'audit_event' => 'created',
    ]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Activity/AuditList')
            ->where('filters.auditable_type', $audit->auditable_type)
            ->where('filters.audit_event', 'created')
            ->where('audits.data.0.id', $audit->id)
            ->where('audits.data.0.action', 'Server created')
            ->where('audits.data.0.subject', 'Server #'.$server->id)
            ->where('audits.data.0.showUrl', route('auditing.activity.audit.show', $audit))
        );
});

it('shows audit changes to administrators', function () {
    $this->actingAs($this->adminAccount());
    $server = Server::factory()->create(['name' => 'Audited Server']);
    $audit = $server->audits()->where('event', 'created')->firstOrFail();

    $this->get(route('auditing.activity.audit.show', $audit))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Activity/AuditShow')
            ->where('audit.id', $audit->id)
            ->where('audit.event', 'created')
            ->where('audit.subject.text', 'Audited Server')
            ->where('audit.changes', fn ($changes) => collect($changes)->contains(fn ($change) => $change['attribute'] === 'name'
                && $change['newValue']['text'] === 'Audited Server'))
        );
});

it('forbids non-admins from accessing the auditing area', function () {
    $audit = Audit::create([
        'event' => 'created',
        'auditable_type' => Server::class,
        'auditable_id' => 999,
        'old_values' => [],
        'new_values' => [],
    ]);

    $this->actingAs(Account::factory()->create())
        ->get(route('auditing.index'))
        ->assertForbidden();

    $this->actingAs(Account::factory()->create())
        ->get(route('auditing.activity.audit.show', $audit))
        ->assertForbidden();
});

it('includes recent audit records in the management overview', function () {
    $this->actingAs($this->adminAccount());
    $server = Server::factory()->create(['name' => 'Recent Audited Server']);
    $audit = $server->audits()->where('event', 'created')->firstOrFail();

    $this->get(route('manage.index'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('Home')
            ->where('recentActivities', fn ($activities) => $activities->contains(fn ($activity) => $activity['id'] === 'audit-'.$audit->id
                && $activity['action'] === 'Server created'
                && $activity['event'] === 'created'
                && $activity['subject'] === 'Server #'.$server->id
                && $activity['createdAt'] === $audit->created_at->toIso8601String()))
            ->where('activityUrl', route('auditing.index'))
        );
});
