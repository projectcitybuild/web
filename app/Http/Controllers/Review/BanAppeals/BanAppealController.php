<?php

namespace App\Http\Controllers\Review\BanAppeals;

use App\Core\Data\Exceptions\NotImplementedException;
use App\Domains\BanAppeals\Entities\BanAppealStatus;
use App\Domains\BanAppeals\Exceptions\AppealAlreadyDecidedException;
use App\Domains\BanAppeals\Exceptions\NoPlayerForActionException;
use App\Domains\BanAppeals\Notifications\BanAppealUpdatedNotification;
use App\Domains\BanAppeals\UseCases\UpdateBanAppeal;
use App\Domains\Bans\Exceptions\NotBannedException;
use App\Http\Requests\BanAppealUpdateRequest;
use App\Models\BanAppeal;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class BanAppealController
{
    public function index()
    {
        Gate::authorize('viewAny', BanAppeal::class);

        // Get ban appeals paginated in the order:
        // Pending appeal (newest first), then all other appeals (newest first)
        $banAppeals = BanAppeal::orderByRaw('FIELD(status, '.BanAppealStatus::PENDING->value.') DESC')
            ->orderBy('created_at', 'desc')
            ->cursorPaginate(50);

        return Inertia::render(
            'BanAppeals/BanAppealList',
            compact('banAppeals'),
        );
    }

    public function show(BanAppeal $banAppeal)
    {
        Gate::authorize('view', $banAppeal);

        $banAppeal->load([
            'gamePlayerBan.bannerPlayer',
            'gamePlayerBan.bannedPlayer',
            'deciderPlayer',
        ]);

        return Inertia::render(
            'BanAppeals/BanAppealShow',
            compact('banAppeal'),
        );
    }

    public function update(
        UpdateBanAppeal $useCase,
        BanAppealUpdateRequest $request,
        BanAppeal $banAppeal,
    ) {
        Gate::authorize('update', $banAppeal);

        try {
            $useCase->execute(
                banAppeal: $banAppeal,
                decidingAccount: $request->user(),
                decisionNote: $request->get('decision_note'),
                status: BanAppealStatus::from($request->get('status'))
            );
        } catch (NotImplementedException $e) {
            throw ValidationException::withMessages([
                'error' => ['This unban decision is not supported currently. Please contact an admin.'],
            ]);
        } catch (NotBannedException $e) {
            throw ValidationException::withMessages([
                'error' => ['Unable to unban player, they are not currently banned.'],
            ]);
        } catch (AppealAlreadyDecidedException $e) {
            throw ValidationException::withMessages([
                'error' => ['This appeal has already been decided.'],
            ]);
        } catch (NoPlayerForActionException $e) {
            throw ValidationException::withMessages([
                'error' => ['Please link a Minecraft account before deciding ban appeals.'],
            ]);
        }

        $banAppeal->notify(new BanAppealUpdatedNotification($banAppeal->showLink()));

        return redirect()->route('manage.ban-appeals.show', $banAppeal);
    }
}
