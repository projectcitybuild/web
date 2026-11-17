<?php

use App\Models\Account;
use App\Models\Audit;
use App\Models\MinecraftPlayer;
use App\Models\PlayerOpElevation;
use App\Models\Server;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    config([
        'audit.console' => true,
        'inertia.ssr.enabled' => false,
    ]);
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

it('renders player operator elevations with duration details', function () {
    $this->actingAs($this->adminAccount());
    $this->travelTo('2026-07-14 12:00:00');

    $activePlayer = MinecraftPlayer::factory()->create(['alias' => 'Active Player']);
    $endedPlayer = MinecraftPlayer::factory()->create(['alias' => 'Ended Player']);

    PlayerOpElevation::create([
        'player_id' => $endedPlayer->id,
        'reason' => 'Finished maintenance',
        'started_at' => now()->subDay(),
        'ended_at' => now()->subHours(20),
    ]);

    PlayerOpElevation::create([
        'player_id' => $activePlayer->id,
        'reason' => 'Current maintenance',
        'started_at' => now()->subHour(),
        'ended_at' => now()->addHour(),
    ]);

    $this->get(route('auditing.op-elevations.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('OpElevations/OpElevationList')
            ->where('elevations.data.0.player', 'Active Player')
            ->where('elevations.data.0.reason', 'Current maintenance')
            ->where('elevations.data.0.startedAt', '2026-07-14T11:00:00+00:00')
            ->where('elevations.data.0.endedAt', '2026-07-14T13:00:00+00:00')
            ->where('elevations.data.0.isActive', true)
            ->where('elevations.data.1.player', 'Ended Player')
            ->where('elevations.data.1.reason', 'Finished maintenance')
            ->where('elevations.data.1.isActive', false)
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

    $this->actingAs(Account::factory()->create())
        ->get(route('auditing.op-elevations.index'))
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
