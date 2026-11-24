<?php

use App\Domains\Permissions\WebReviewPermission;
use App\Domains\PlayerReports\Data\PlayerReportStatus;
use App\Domains\PlayerReports\Data\PlayerReportVisibility;
use App\Models\Account;
use App\Models\MinecraftPlayer;
use App\Models\Permission;
use App\Models\PlayerReport;
use App\Models\Role;
use App\Notifications\PlayerReportCompletedNotification;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

function createPlayerReportReviewer(array $permissions): Account
{
    $account = Account::factory()->hasFinishedTotp()->create();
    $role = Role::factory()->staff()->create();

    $permissionIds = collect($permissions)
        ->map(fn (WebReviewPermission $permission): int => Permission::create([
            'name' => $permission->value,
        ])->id);

    $account->roles()->attach($role->id);
    $role->permissions()->attach($permissionIds);

    return $account;
}

it('hides administrators-only reports from regular reviewers', function () {
    config(['inertia.ssr.enabled' => false]);

    $reviewer = createPlayerReportReviewer([WebReviewPermission::PLAYER_REPORTS_VIEW]);
    $standardReport = PlayerReport::factory()->create();
    $administratorsOnlyReport = PlayerReport::factory()->create([
        'visibility' => PlayerReportVisibility::ADMINISTRATORS,
    ]);

    $this->actingAs($reviewer)
        ->get(route('review.player-reports.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('PlayerReports/PlayerReportList')
            ->where('reports.total', 1)
            ->where('reports.data.0.id', $standardReport->id)
        );

    $this->actingAs($reviewer)
        ->get(route('review.player-reports.show', $administratorsOnlyReport))
        ->assertNotFound();

    $administrator = Account::factory()->hasFinishedTotp()->create();
    $administratorRole = Role::factory()->create([
        'is_admin' => true,
        'role_type' => 'staff',
    ]);
    $administrator->roles()->attach($administratorRole->id);

    $this->actingAs($administrator)
        ->get(route('review.player-reports.show', $administratorsOnlyReport))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('PlayerReports/PlayerReportShow')
            ->where('report.id', $administratorsOnlyReport->id)
        );
});

it('provides avatar details for assigned accounts', function () {
    config(['inertia.ssr.enabled' => false]);

    $reviewer = createPlayerReportReviewer([WebReviewPermission::PLAYER_REPORTS_VIEW]);
    $assignedAccount = Account::factory()->create(['username' => 'assigned-account']);
    $staffRole = Role::factory()->staff()->create();
    $assignedAccount->roles()->attach($staffRole->id);
    $player = MinecraftPlayer::factory()
        ->for($assignedAccount)
        ->create(['alias' => 'AssignedPlayer']);
    $report = PlayerReport::factory()->create();
    $report->assignees()->attach($assignedAccount->id, [
        'assigned_by_account_id' => $reviewer->id,
    ]);

    $this->actingAs($reviewer)
        ->get(route('review.player-reports.show', $report))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('report.assignees.0.id', $assignedAccount->id)
            ->where('report.assignees.0.name', 'assigned-account')
            ->where('report.assignees.0.alias', 'AssignedPlayer')
            ->where('report.assignees.0.uuid', $player->uuid)
            ->where('canAssignCurrentAccount', true)
        );
});

it('includes assigned user avatars in the report list', function () {
    config(['inertia.ssr.enabled' => false]);

    $reviewer = createPlayerReportReviewer([WebReviewPermission::PLAYER_REPORTS_VIEW]);
    $assignedAccount = Account::factory()->create(['username' => 'assigned-account']);
    $staffRole = Role::factory()->staff()->create();
    $assignedAccount->roles()->attach($staffRole->id);
    $player = MinecraftPlayer::factory()
        ->for($assignedAccount)
        ->create(['alias' => 'AssignedPlayer']);
    $report = PlayerReport::factory()->create();
    $report->assignees()->attach($assignedAccount->id, [
        'assigned_by_account_id' => $reviewer->id,
    ]);

    $this->actingAs($reviewer)
        ->get(route('review.player-reports.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('reports.data.0.id', $report->id)
            ->where('reports.data.0.assignees.0.alias', 'AssignedPlayer')
            ->where('reports.data.0.assignees.0.uuid', $player->uuid)
        );
});

it('searches eligible accounts by linked player alias', function () {
    $reviewer = createPlayerReportReviewer([
        WebReviewPermission::PLAYER_REPORTS_VIEW,
        WebReviewPermission::PLAYER_REPORTS_UPDATE,
    ]);
    $eligibleAccount = Account::factory()->create(['username' => 'eligible-account']);
    $eligibleRole = Role::factory()->staff()->create();
    $eligibleAccount->roles()->attach($eligibleRole->id);
    $eligiblePlayer = MinecraftPlayer::factory()
        ->for($eligibleAccount)
        ->create(['alias' => 'CaseModerator']);

    $ineligibleAccount = Account::factory()->create();
    MinecraftPlayer::factory()->for($ineligibleAccount)->create(['alias' => 'CasePlayer']);
    $report = PlayerReport::factory()->create();

    $this->actingAs($reviewer)
        ->getJson(route('review.player-reports.assignable-accounts.search', $report).'?search=Case')
        ->assertOk()
        ->assertExactJson([
            [
                'id' => $eligibleAccount->id,
                'name' => 'eligible-account',
                'alias' => 'CaseModerator',
                'uuid' => $eligiblePlayer->uuid,
            ],
        ]);
});

it('does not expose administrator-only account suggestions to staff', function () {
    $reviewer = createPlayerReportReviewer([
        WebReviewPermission::PLAYER_REPORTS_VIEW,
        WebReviewPermission::PLAYER_REPORTS_UPDATE,
    ]);
    $report = PlayerReport::factory()->create([
        'visibility' => PlayerReportVisibility::ADMINISTRATORS,
    ]);

    $this->actingAs($reviewer)
        ->getJson(route('review.player-reports.assignable-accounts.search', $report).'?search=Admin')
        ->assertNotFound();
});

it('requires assignment permission to search for accounts', function () {
    $reviewer = createPlayerReportReviewer([WebReviewPermission::PLAYER_REPORTS_VIEW]);
    $report = PlayerReport::factory()->create();

    $this->actingAs($reviewer)
        ->getJson(route('review.player-reports.assignable-accounts.search', $report).'?search=Admin')
        ->assertForbidden();
});

it('requires a two-character account search', function () {
    $reviewer = createPlayerReportReviewer([
        WebReviewPermission::PLAYER_REPORTS_VIEW,
        WebReviewPermission::PLAYER_REPORTS_UPDATE,
    ]);
    $report = PlayerReport::factory()->create();

    $this->actingAs($reviewer)
        ->getJson(route('review.player-reports.assignable-accounts.search', $report).'?search=a')
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['search']);
});

it('lets permitted staff assign reports and email an optional completion message', function () {
    Notification::fake();
    $reviewer = createPlayerReportReviewer([
        WebReviewPermission::PLAYER_REPORTS_VIEW,
        WebReviewPermission::PLAYER_REPORTS_UPDATE,
    ]);
    $otherStaff = Account::factory()->create();
    $otherStaffRole = Role::factory()->staff()->create();
    $otherStaff->roles()->attach($otherStaffRole->id);
    $report = PlayerReport::factory()->create([
        'reporter_email' => 'reporter@example.com',
    ]);

    $this->actingAs($reviewer)
        ->put(route('review.player-reports.assignees.update', $report), [
            'assignee_ids' => [$otherStaff->id],
        ])
        ->assertRedirect(route('review.player-reports.show', $report));

    $this->post(route('review.player-reports.assignees.self', $report))
        ->assertRedirect(route('review.player-reports.show', $report));

    $this->assertDatabaseHas('player_report_assignments', [
        'player_report_id' => $report->id,
        'account_id' => $otherStaff->id,
        'assigned_by_account_id' => $reviewer->id,
    ]);
    $this->assertDatabaseHas('player_report_assignments', [
        'player_report_id' => $report->id,
        'account_id' => $reviewer->id,
        'assigned_by_account_id' => $reviewer->id,
    ]);

    $this->put(route('review.player-reports.complete', $report), [
        'completion_message' => 'We have reviewed your report and taken appropriate action.',
    ])
        ->assertRedirect(route('review.player-reports.show', $report));

    $this->assertDatabaseHas('player_reports', [
        'id' => $report->id,
        'status' => PlayerReportStatus::COMPLETED->value,
        'resolved_by_account_id' => $reviewer->id,
        'completion_message' => 'We have reviewed your report and taken appropriate action.',
    ]);
    expect($report->fresh()->status)->toBe(PlayerReportStatus::COMPLETED);
    Notification::assertSentOnDemand(
        PlayerReportCompletedNotification::class,
        fn (PlayerReportCompletedNotification $notification, array $channels, object $notifiable): bool => $notification->message === 'We have reviewed your report and taken appropriate action.'
            && $notifiable->routes['mail'] === 'reporter@example.com',
    );
});

it('rejects assigning a report to an account without a staff role', function () {
    $reviewer = createPlayerReportReviewer([
        WebReviewPermission::PLAYER_REPORTS_VIEW,
        WebReviewPermission::PLAYER_REPORTS_UPDATE,
    ]);
    $nonStaff = Account::factory()->create();
    $report = PlayerReport::factory()->create();

    $this->actingAs($reviewer)
        ->put(route('review.player-reports.assignees.update', $report), [
            'assignee_ids' => [$nonStaff->id],
        ])
        ->assertSessionHasErrors(['assignee_ids.0']);

    $this->assertDatabaseCount('player_report_assignments', 0);
});

it('restricts administrators-only reports to administrator assignees', function () {
    $administrator = Account::factory()->hasFinishedTotp()->create();
    $administratorRole = Role::factory()->create([
        'is_admin' => true,
        'role_type' => 'staff',
    ]);
    $administrator->roles()->attach($administratorRole->id);
    $reviewer = Account::factory()->create();
    $reviewerRole = Role::factory()->staff()->create();
    $reviewer->roles()->attach($reviewerRole->id);
    $report = PlayerReport::factory()->create([
        'visibility' => PlayerReportVisibility::ADMINISTRATORS,
    ]);

    $this->actingAs($administrator)
        ->put(route('review.player-reports.assignees.update', $report), [
            'assignee_ids' => [$reviewer->id],
        ])
        ->assertSessionHasErrors(['assignee_ids.0']);

    $this->assertDatabaseCount('player_report_assignments', 0);
});

it('lets administrators change report visibility and removes ineligible assignees', function () {
    $administrator = Account::factory()->hasFinishedTotp()->create();
    $administratorRole = Role::factory()->create([
        'is_admin' => true,
        'role_type' => 'staff',
    ]);
    $administrator->roles()->attach($administratorRole->id);

    $staffAccount = Account::factory()->create();
    $staffRole = Role::factory()->staff()->create();
    $staffAccount->roles()->attach($staffRole->id);

    $report = PlayerReport::factory()->create();
    $report->assignees()->attach([
        $administrator->id => ['assigned_by_account_id' => $administrator->id],
        $staffAccount->id => ['assigned_by_account_id' => $administrator->id],
    ]);

    $this->actingAs($administrator)
        ->put(route('review.player-reports.visibility.update', $report), [
            'visibility' => PlayerReportVisibility::ADMINISTRATORS->value,
        ])
        ->assertRedirect(route('review.player-reports.show', $report));

    $this->assertDatabaseHas('player_reports', [
        'id' => $report->id,
        'visibility' => PlayerReportVisibility::ADMINISTRATORS->value,
    ]);
    $this->assertDatabaseHas('player_report_assignments', [
        'player_report_id' => $report->id,
        'account_id' => $administrator->id,
    ]);
    $this->assertDatabaseMissing('player_report_assignments', [
        'player_report_id' => $report->id,
        'account_id' => $staffAccount->id,
    ]);

    $this->put(route('review.player-reports.visibility.update', $report), [
        'visibility' => PlayerReportVisibility::STANDARD->value,
    ])->assertRedirect(route('review.player-reports.show', $report));

    $this->assertDatabaseHas('player_reports', [
        'id' => $report->id,
        'visibility' => PlayerReportVisibility::STANDARD->value,
    ]);

    $this->put(route('review.player-reports.visibility.update', $report), [
        'visibility' => 'all-staff',
    ])->assertSessionHasErrors(['visibility']);
});

it('does not let staff change report visibility', function () {
    $reviewer = createPlayerReportReviewer([
        WebReviewPermission::PLAYER_REPORTS_VIEW,
        WebReviewPermission::PLAYER_REPORTS_UPDATE,
    ]);
    $report = PlayerReport::factory()->create();

    $this->actingAs($reviewer)
        ->put(route('review.player-reports.visibility.update', $report), [
            'visibility' => PlayerReportVisibility::ADMINISTRATORS->value,
        ])
        ->assertForbidden();

    $this->assertDatabaseHas('player_reports', [
        'id' => $report->id,
        'visibility' => PlayerReportVisibility::STANDARD->value,
    ]);
});

it('completes a report without emailing when no completion message is entered', function () {
    Notification::fake();
    $reviewer = createPlayerReportReviewer([
        WebReviewPermission::PLAYER_REPORTS_VIEW,
        WebReviewPermission::PLAYER_REPORTS_UPDATE,
    ]);
    $report = PlayerReport::factory()->create([
        'reporter_email' => 'reporter@example.com',
    ]);

    $this->actingAs($reviewer)
        ->put(route('review.player-reports.complete', $report), [
            'completion_message' => '',
        ])
        ->assertRedirect(route('review.player-reports.show', $report));

    $this->assertDatabaseHas('player_reports', [
        'id' => $report->id,
        'status' => PlayerReportStatus::COMPLETED->value,
        'completion_message' => null,
    ]);
    Notification::assertNothingSent();
});

it('does not allow reviewers without update permission to assign reports', function () {
    $reviewer = createPlayerReportReviewer([WebReviewPermission::PLAYER_REPORTS_VIEW]);
    $report = PlayerReport::factory()->create();

    $this->actingAs($reviewer)
        ->put(route('review.player-reports.assignees.update', $report), [
            'assignee_ids' => [$reviewer->id],
        ])
        ->assertForbidden();

    $this->assertDatabaseCount('player_report_assignments', 0);
});

it('forbids staff from deleting player reports', function () {
    $reviewer = createPlayerReportReviewer([WebReviewPermission::PLAYER_REPORTS_VIEW]);
    $report = PlayerReport::factory()->create();

    $this->actingAs($reviewer)
        ->delete(route('review.player-reports.destroy', $report))
        ->assertForbidden();

    $this->assertDatabaseHas('player_reports', [
        'id' => $report->id,
    ]);
});

it('lets administrators delete reports and their evidence', function () {
    Storage::fake('player-reports');
    $administrator = Account::factory()->hasFinishedTotp()->create();
    $administratorRole = Role::factory()->create([
        'is_admin' => true,
        'role_type' => 'staff',
    ]);
    $administrator->roles()->attach($administratorRole->id);
    $report = PlayerReport::factory()->create();
    $report->assignees()->attach($administrator->id, [
        'assigned_by_account_id' => $administrator->id,
    ]);
    Storage::disk('player-reports')->put('player-reports/private-evidence.pdf', 'private evidence');
    $attachment = $report->attachments()->create([
        'path' => 'player-reports/private-evidence.pdf',
        'original_name' => 'private-evidence.pdf',
        'mime_type' => 'application/pdf',
        'file_size' => 16,
    ]);

    $this->actingAs($administrator)
        ->delete(route('review.player-reports.destroy', $report))
        ->assertRedirect(route('review.player-reports.index'));

    $this->assertDatabaseMissing('player_reports', [
        'id' => $report->id,
    ]);
    $this->assertDatabaseMissing('player_report_assignments', [
        'player_report_id' => $report->id,
    ]);
    $this->assertDatabaseMissing('player_report_attachments', [
        'id' => $attachment->id,
    ]);
    Storage::disk('player-reports')->assertMissing($attachment->path);
});

it('does not serve administrators-only attachments to regular reviewers', function () {
    Storage::fake('player-reports');
    $reviewer = createPlayerReportReviewer([WebReviewPermission::PLAYER_REPORTS_VIEW]);
    $report = PlayerReport::factory()->create([
        'visibility' => PlayerReportVisibility::ADMINISTRATORS,
    ]);
    Storage::disk('player-reports')->put('player-reports/evidence.pdf', 'private evidence');
    $attachment = $report->attachments()->create([
        'path' => 'player-reports/evidence.pdf',
        'original_name' => 'evidence.pdf',
        'mime_type' => 'application/pdf',
        'file_size' => 16,
    ]);
    Storage::disk('player-reports')->put('player-reports/evidence.png', 'image content');
    $imageAttachment = $report->attachments()->create([
        'path' => 'player-reports/evidence.png',
        'original_name' => 'evidence.png',
        'mime_type' => 'image/png',
        'file_size' => 13,
    ]);

    $this->actingAs($reviewer)
        ->get(route('review.player-reports.attachments.download', [$report, $attachment]))
        ->assertNotFound();
    $this->get(route('review.player-reports.attachments.preview', [$report, $imageAttachment]))
        ->assertNotFound();

    Storage::disk('player-reports')->assertExists($attachment->path);

    $administrator = Account::factory()->hasFinishedTotp()->create();
    $administratorRole = Role::factory()->create([
        'is_admin' => true,
        'role_type' => 'staff',
    ]);
    $administrator->roles()->attach($administratorRole->id);

    $this->actingAs($administrator)
        ->get(route('review.player-reports.attachments.download', [$report, $attachment]))
        ->assertOk()
        ->assertDownload('evidence.pdf');
    $this->get(route('review.player-reports.attachments.preview', [$report, $imageAttachment]))
        ->assertOk()
        ->assertHeader('Content-Type', 'image/png')
        ->assertHeader('X-Content-Type-Options', 'nosniff');
    $this->get(route('review.player-reports.attachments.preview', [$report, $attachment]))
        ->assertNotFound();
});

it('escapes report content in completion emails', function () {
    $html = (string) (new PlayerReportCompletedNotification(
        reportId: 42,
        reportSubject: '<script>alert(1)</script>',
        message: '<img src=x onerror=alert(1)>',
    ))->toMail(new AnonymousNotifiable)->render();

    expect($html)
        ->toContain('&lt;script&gt;')
        ->toContain('&lt;img')
        ->not->toContain('<script>')
        ->not->toContain('<img src=x onerror=alert(1)>');
});
