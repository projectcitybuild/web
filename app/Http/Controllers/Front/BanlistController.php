<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\WebController;
use App\Models\GamePlayerBan;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

final class BanlistController extends WebController
{
    public function index(Request $request): View|JsonResponse
    {
        $query = $request->string('query')->trim()->toString();
        $sortOptions = $request->validate([
            'sort' => ['nullable', 'string', Rule::in(['name', 'reason', 'created_at'])],
            'direction' => ['nullable', 'string', Rule::in(['asc', 'desc'])],
        ]);

        $bans = GamePlayerBan::query()
            ->when($query !== '', function (Builder $bans) use ($query): void {
                $bans->where(function (Builder $bans) use ($query): void {
                    $likeQuery = "%{$query}%";

                    $bans->where('banned_alias_at_time', 'like', $likeQuery)
                        ->orWhereHas('bannedPlayer', function (Builder $player) use ($likeQuery): void {
                            $player->where('alias', 'like', $likeQuery)
                                ->orWhere('uuid', 'like', $likeQuery);
                        });
                });
            })
            ->with('bannedPlayer:id,uuid,alias')
            ->active();

        $sort = $sortOptions['sort'] ?? null;
        $direction = $sortOptions['direction'] ?? null;

        if ($sort !== null && $direction !== null) {
            if ($sort === 'name') {
                $bans->leftJoin('players as banned_players', 'banned_players.id', '=', 'game_player_bans.banned_player_id')
                    ->select('game_player_bans.*')
                    ->orderByRaw("COALESCE(banned_players.alias, game_player_bans.banned_alias_at_time) {$direction}");
            } else {
                $bans->orderBy("game_player_bans.{$sort}", $direction);
            }

            $bans->orderBy('game_player_bans.id');
        } else {
            $bans->latest('game_player_bans.created_at');
        }

        $bans = $bans->paginate(50)
            ->withQueryString()
            ->through(fn (GamePlayerBan $ban): array => $this->presentBan($ban));

        if ($request->expectsJson()) {
            return response()->json($bans);
        }

        return view('front.pages.bans.index', compact('bans', 'query'));
    }

    public function show(Request $request, GamePlayerBan $ban)
    {
        return view('front.pages.bans.show', compact('ban'));
    }

    /**
     * @return array{
     *     id: int|string,
     *     name: string,
     *     uuid: string|null,
     *     avatar_url: string|null,
     *     reason: string|null,
     *     created_at: string|null,
     *     appeal_url: string,
     *     details_url: string
     * }
     */
    private function presentBan(GamePlayerBan $ban): array
    {
        $uuid = $ban->bannedPlayer?->uuid;

        return [
            'id' => $ban->getKey(),
            'name' => $ban->bannedPlayer?->alias ?? $ban->banned_alias_at_time,
            'uuid' => $uuid,
            'avatar_url' => $uuid === null ? null : "https://minotar.net/helm/{$uuid}/16",
            'reason' => $ban->reason,
            'created_at' => $ban->created_at?->toIso8601String(),
            'appeal_url' => route('front.appeal.form.prefilled', $ban),
            'details_url' => route('front.bans.details', $ban),
        ];
    }
}
