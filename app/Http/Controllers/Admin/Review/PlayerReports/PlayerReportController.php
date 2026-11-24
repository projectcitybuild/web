<?php

namespace App\Http\Controllers\Admin\Review\PlayerReports;

use App\Domains\Permissions\AuthorizesPermissions;
use App\Domains\Permissions\WebReviewPermission;
use App\Domains\PlayerReports\Data\PlayerReportStatus;
use App\Domains\PlayerReports\Data\PlayerReportVisibility;
use App\Http\Controllers\Admin\RendersAdminApp;
use App\Http\Controllers\WebController;
use App\Http\Data\AdminPlayerReportData;
use App\Models\Account;
use App\Models\PlayerReport;
use App\Models\PlayerReportAttachment;
use App\Notifications\PlayerReportCompletedNotification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Response;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class PlayerReportController extends WebController
{
    use AuthorizesPermissions;
    use RendersAdminApp;

    public function index(Request $request): Response
    {
        $this->requires(WebReviewPermission::PLAYER_REPORTS_VIEW);

        /** @var Account $account */
        $account = $request->user();
        $statusFilter = $request->string('status')->toString();
        $status = PlayerReportStatus::tryFrom($statusFilter);

        $reports = PlayerReport::query()
            ->when(! $account->isAdmin(), fn (Builder $query) => $query->where('visibility', PlayerReportVisibility::STANDARD->value))
            ->when($status !== null, fn (Builder $query) => $query->where('status', $status->value))
            ->with(['reporterAccount', 'assignees.minecraftAccount', 'attachments'])
            ->orderByRaw('FIELD(status, ?) DESC', [PlayerReportStatus::OPEN->value])
            ->orderByDesc('id')
            ->paginate(30)
            ->through(fn (PlayerReport $report): array => AdminPlayerReportData::fromModel($report)->toArray());

        return $this->inertiaRender('PlayerReports/PlayerReportList', [
            'reports' => $reports,
            'filters' => ['status' => $statusFilter],
        ]);
    }

    public function show(Request $request, PlayerReport $playerReport): Response
    {
        $this->requires(WebReviewPermission::PLAYER_REPORTS_VIEW);
        $this->authorizeVisibility($request, $playerReport);

        $playerReport->load([
            'reporterAccount',
            'assignees.minecraftAccount' => fn (HasMany $query) => $query->orderBy('id'),
            'attachments',
        ]);

        return $this->inertiaRender('PlayerReports/PlayerReportShow', [
            'report' => AdminPlayerReportData::fromModel($playerReport),
            'currentAccountId' => $request->user()->getAuthIdentifier(),
            'canAssignCurrentAccount' => $this->assignableAccountsQuery($playerReport)
                ->whereKey($request->user()->getAuthIdentifier())
                ->exists(),
            'canUpdate' => $this->can(WebReviewPermission::PLAYER_REPORTS_UPDATE),
        ]);
    }

    public function destroy(Request $request, PlayerReport $playerReport): RedirectResponse
    {
        abort_unless($request->user()->isAdmin(), 403);

        $attachmentPaths = $playerReport->attachments()->pluck('path')->all();
        if ($attachmentPaths !== [] && ! Storage::disk('player-reports')->delete($attachmentPaths)) {
            throw new RuntimeException('Unable to delete player report attachments.');
        }

        $playerReport->delete();

        return to_route('review.player-reports.index')
            ->with(['success' => 'Player report deleted successfully.']);
    }

    public function searchAssignableAccounts(Request $request, PlayerReport $playerReport): JsonResponse
    {
        $this->requires(WebReviewPermission::PLAYER_REPORTS_UPDATE);
        $this->authorizeVisibility($request, $playerReport);
        $this->ensureOpen($playerReport);

        $validated = $request->validate([
            'search' => ['required', 'string', 'min:2', 'max:64'],
        ]);
        $aliasSearch = '%'.$validated['search'].'%';

        $accounts = $this->assignableAccountsQuery($playerReport)
            ->whereNotIn('accounts.id', $playerReport->assignees()->select('accounts.id'))
            ->whereHas(
                'minecraftAccount',
                fn (Builder $query) => $query->where('alias', 'like', $aliasSearch),
            )
            ->with([
                'minecraftAccount' => fn (HasMany $query) => $query
                    ->where('alias', 'like', $aliasSearch)
                    ->orderBy('id'),
            ])
            ->orderBy('username')
            ->limit(10)
            ->get(['accounts.id', 'accounts.username'])
            ->map(function (Account $account): array {
                $player = $account->minecraftAccount->first();

                return [
                    'id' => $account->id,
                    'name' => $account->username,
                    'alias' => $player->alias,
                    'uuid' => $player->uuid,
                ];
            })
            ->values();

        return response()->json($accounts);
    }

    public function updateAssignees(Request $request, PlayerReport $playerReport): RedirectResponse
    {
        $this->requires(WebReviewPermission::PLAYER_REPORTS_UPDATE);
        $this->authorizeVisibility($request, $playerReport);
        $this->ensureOpen($playerReport);

        $staffIds = $this->assignableAccountsQuery($playerReport)
            ->pluck('id')
            ->all();

        $validated = $request->validate([
            'assignee_ids' => ['array', 'max:20'],
            'assignee_ids.*' => ['required', 'integer', Rule::in($staffIds)],
        ]);

        $playerReport->assignees()->syncWithPivotValues(
            array_unique($validated['assignee_ids'] ?? []),
            ['assigned_by_account_id' => $request->user()->getAuthIdentifier()],
        );

        return to_route('review.player-reports.show', $playerReport)
            ->with(['success' => 'Report assignments updated.']);
    }

    public function updateVisibility(Request $request, PlayerReport $playerReport): RedirectResponse
    {
        abort_unless($request->user()->isAdmin(), 403);

        $validated = $request->validate([
            'visibility' => ['required', Rule::enum(PlayerReportVisibility::class)],
        ]);

        DB::transaction(function () use ($playerReport, $validated): void {
            $playerReport->forceFill([
                'visibility' => PlayerReportVisibility::from($validated['visibility']),
            ])->save();

            $assignableAccountIds = $this->assignableAccountsQuery($playerReport)->pluck('accounts.id')->all();
            $assignedAccountIds = $playerReport->assignees()->pluck('accounts.id')->all();

            $playerReport->assignees()->detach(array_values(array_diff($assignedAccountIds, $assignableAccountIds)));
        });

        return to_route('review.player-reports.show', $playerReport)
            ->with(['success' => 'Report visibility updated.']);
    }

    public function assignToSelf(Request $request, PlayerReport $playerReport): RedirectResponse
    {
        $this->requires(WebReviewPermission::PLAYER_REPORTS_UPDATE);
        $this->authorizeVisibility($request, $playerReport);
        $this->ensureOpen($playerReport);

        $account = $request->user();
        abort_unless(
            $this->assignableAccountsQuery($playerReport)->whereKey($account->getAuthIdentifier())->exists(),
            403,
        );

        $playerReport->assignees()->syncWithoutDetaching([
            $account->getAuthIdentifier() => [
                'assigned_by_account_id' => $account->getAuthIdentifier(),
            ],
        ]);

        return to_route('review.player-reports.show', $playerReport)
            ->with(['success' => 'Report assigned to you.']);
    }

    public function complete(Request $request, PlayerReport $playerReport): RedirectResponse
    {
        $this->requires(WebReviewPermission::PLAYER_REPORTS_UPDATE);
        $this->authorizeVisibility($request, $playerReport);
        $this->ensureOpen($playerReport);

        $validated = $request->validate([
            'completion_message' => ['nullable', 'string', 'max:5000'],
        ]);

        $playerReport->forceFill([
            'status' => PlayerReportStatus::COMPLETED,
            'completion_message' => $validated['completion_message'] ?? null,
            'resolved_by_account_id' => $request->user()->getAuthIdentifier(),
            'resolved_at' => now(),
        ])->save();

        if ($playerReport->reporter_email !== null && filled($playerReport->completion_message)) {
            $notification = new PlayerReportCompletedNotification(
                reportId: $playerReport->id,
                reportSubject: $playerReport->subject,
                message: $playerReport->completion_message,
            );
            Notification::route('mail', $playerReport->reporter_email)
                ->notify($notification);
        }

        return to_route('review.player-reports.show', $playerReport)
            ->with(['success' => 'Player report marked as complete.']);
    }

    public function downloadAttachment(
        Request $request,
        PlayerReport $playerReport,
        PlayerReportAttachment $attachment,
    ): StreamedResponse {
        $this->requires(WebReviewPermission::PLAYER_REPORTS_VIEW);
        $this->authorizeVisibility($request, $playerReport);
        abort_unless($attachment->player_report_id === $playerReport->id, 404);

        return Storage::disk('player-reports')->download(
            $attachment->path,
            $attachment->original_name,
            ['Content-Type' => $attachment->mime_type],
        );
    }

    public function previewAttachment(
        Request $request,
        PlayerReport $playerReport,
        PlayerReportAttachment $attachment,
    ): StreamedResponse {
        $this->requires(WebReviewPermission::PLAYER_REPORTS_VIEW);
        $this->authorizeVisibility($request, $playerReport);
        abort_unless($attachment->player_report_id === $playerReport->id, 404);
        abort_unless(in_array($attachment->mime_type, ['image/jpeg', 'image/png', 'image/webp'], true), 404);
        $disk = Storage::disk('player-reports');
        abort_unless($disk->exists($attachment->path), 404);

        return $disk->response(
            $attachment->path,
            headers: [
                'Content-Type' => $attachment->mime_type,
                'X-Content-Type-Options' => 'nosniff',
                'Cache-Control' => 'private, no-store',
            ],
        );
    }

    private function authorizeVisibility(Request $request, PlayerReport $playerReport): void
    {
        if (
            $playerReport->visibility === PlayerReportVisibility::ADMINISTRATORS
            && ! $request->user()->isAdmin()
        ) {
            abort(404);
        }
    }

    private function ensureOpen(PlayerReport $playerReport): void
    {
        if ($playerReport->status !== PlayerReportStatus::OPEN) {
            throw ValidationException::withMessages([
                'status' => 'This report has already been completed.',
            ]);
        }
    }

    private function assignableAccountsQuery(PlayerReport $playerReport): Builder
    {
        return Account::query()->whereHas(
            'roles',
            function (Builder $query) use ($playerReport): void {
                if ($playerReport->visibility === PlayerReportVisibility::ADMINISTRATORS) {
                    $query->where('is_admin', true);

                    return;
                }

                $query->where(fn (Builder $query) => $query
                    ->where('role_type', 'staff')
                    ->orWhere('is_admin', true));
            },
        );
    }
}
