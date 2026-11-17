<?php

namespace App\Http\Controllers\Admin\Manage\PlayerMail;

use App\Domains\Permissions\AuthorizesPermissions;
use App\Domains\Permissions\WebManagePermission;
use App\Domains\PlayerMail\Data\PlayerMailBroadcastStatus;
use App\Domains\PlayerMail\UseCases\BroadcastPlayerMail;
use App\Http\Controllers\Admin\RendersAdminApp;
use App\Http\Controllers\WebController;
use App\Models\Account;
use App\Models\MinecraftPlayer;
use App\Models\PlayerMailBroadcast;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Response;

class PlayerMailBroadcastController extends WebController
{
    use AuthorizesPermissions;
    use RendersAdminApp;

    public function index(Request $request): Response
    {
        $this->requires(WebManagePermission::PLAYER_MAIL_SEND);

        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:191'],
            'status' => ['nullable', Rule::in(array_map(
                fn (PlayerMailBroadcastStatus $status): string => $status->value,
                PlayerMailBroadcastStatus::cases(),
            ))],
        ]);

        $broadcasts = PlayerMailBroadcast::query()
            ->with(['mail.createdByAccount'])
            ->when($filters['search'] ?? null, function (Builder $query, string $search): void {
                $query->whereHas('mail', fn (Builder $mailQuery) => $mailQuery->where('subject', 'like', '%'.$search.'%'));
            })
            ->when($filters['status'] ?? null, fn (Builder $query, string $status): Builder => $query->where('status', $status))
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString()
            ->through(fn (PlayerMailBroadcast $broadcast): array => [
                'id' => $broadcast->id,
                'subject' => $broadcast->mail->subject,
                'sender' => $broadcast->mail->createdByAccount?->username ?? 'Deleted staff account',
                'recipients_count' => $broadcast->recipients_count,
                'status' => $broadcast->status->value,
                'created_at' => $broadcast->created_at->toISOString(),
            ]);

        return $this->inertiaRender('PlayerMail/Index', [
            'broadcasts' => $broadcasts,
            'filters' => [
                'search' => $filters['search'] ?? '',
                'status' => $filters['status'] ?? '',
            ],
        ]);
    }

    public function create(Request $request): Response
    {
        $this->requires(WebManagePermission::PLAYER_MAIL_SEND);

        $account = $request->user();
        abort_unless($account instanceof Account, 401);

        return $this->inertiaRender('PlayerMail/Create', [
            'recipientCount' => MinecraftPlayer::query()->count(),
            'sender' => $account->username,
        ]);
    }

    public function show(Request $request, PlayerMailBroadcast $broadcast): Response
    {
        $this->requires(WebManagePermission::PLAYER_MAIL_SEND);

        $broadcast->load('mail.createdByAccount');

        $backFilters = array_filter(
            $request->only(['search', 'status', 'page']),
            fn (mixed $value): bool => $value !== null && $value !== '',
        );

        return $this->inertiaRender('PlayerMail/Show', [
            'broadcast' => [
                'id' => $broadcast->id,
                'subject' => $broadcast->mail->subject,
                'body' => $broadcast->mail->body,
                'sender' => $broadcast->mail->createdByAccount?->username ?? 'Deleted staff account',
                'status' => $broadcast->status->value,
                'recipients_count' => $broadcast->recipients_count,
                'created_at' => $broadcast->created_at->toISOString(),
                'started_at' => $broadcast->started_at?->toISOString(),
                'completed_at' => $broadcast->completed_at?->toISOString(),
            ],
            'audienceEstimate' => MinecraftPlayer::query()
                ->where('id', '<=', $broadcast->max_player_id)
                ->count(),
            'backUrl' => route('manage.player-mail.index', $backFilters),
        ]);
    }

    public function storeFromManage(Request $request, BroadcastPlayerMail $broadcastPlayerMail): RedirectResponse
    {
        $this->requires(WebManagePermission::PLAYER_MAIL_SEND);

        $broadcast = $this->createBroadcast($request, $broadcastPlayerMail);

        return to_route('manage.player-mail.show', $broadcast);
    }

    public function store(Request $request, BroadcastPlayerMail $broadcastPlayerMail): JsonResponse
    {
        $this->requires(WebManagePermission::PLAYER_MAIL_SEND);

        $broadcast = $this->createBroadcast($request, $broadcastPlayerMail);

        return response()->json([
            'id' => $broadcast->id,
            'status' => $broadcast->status->value,
            'recipients_count' => $broadcast->recipients_count,
        ], 202);
    }

    private function createBroadcast(Request $request, BroadcastPlayerMail $broadcastPlayerMail): PlayerMailBroadcast
    {
        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:191'],
            'body' => ['required', 'string', 'max:5000'],
        ]);

        $account = $request->user();
        abort_unless($account instanceof Account, 401);

        return $broadcastPlayerMail->execute(
            account: $account,
            subject: $validated['subject'],
            body: $validated['body'],
        );
    }
}
