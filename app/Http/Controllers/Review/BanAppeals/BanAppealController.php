<?php

namespace App\Http\Controllers\Review\BanAppeals;

use App\Core\Data\Exceptions\NotImplementedException;
use App\Domains\BanAppeals\Data\BanAppealStatus;
use App\Domains\BanAppeals\Exceptions\AppealAlreadyDecidedException;
use App\Domains\BanAppeals\Exceptions\NoPlayerForActionException;
use App\Domains\BanAppeals\Notifications\BanAppealUpdatedNotification;
use App\Domains\BanAppeals\UseCases\UpdateBanAppeal;
use App\Domains\Bans\Exceptions\BanNotFoundException;
use App\Domains\Permissions\AuthorizesPermissions;
use App\Domains\Permissions\WebReviewPermission;
use App\Http\Controllers\Review\RendersReviewApp;
use App\Http\Requests\BanAppealUpdateRequest;
use App\Http\Resources\BanAppealResource;
use App\Models\BanAppeal;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class BanAppealController
{
    use AuthorizesPermissions;
    use RendersReviewApp;

    public function index(Request $request)
    {
        $this->requires(WebReviewPermission::BAN_APPEALS_VIEW);

        // Get ban appeals paginated in the order:
        // Pending appeal (newest first), then all other appeals (newest first)
        $banAppeals = function () {
            return BanAppeal::orderByRaw('FIELD(status, '.BanAppealStatus::PENDING->value.') DESC')
                ->with('gamePlayerBan.bannedPlayer')
                ->orderBy('created_at', 'desc')
                ->paginate(50);
        };

        if ($request->wantsJson()) {
            return $banAppeals();
        }

        return $this->inertiaRender('BanAppeals/BanAppealList', [
            'banAppeals' => Inertia::defer($banAppeals),
        ]);
    }

    public function show(BanAppeal $banAppeal)
    {
        $this->requires(WebReviewPermission::BAN_APPEALS_VIEW);

        $banAppeal->load([
            'account',
            'gamePlayerBan.bannerPlayer',
            'gamePlayerBan.bannedPlayer',
            'deciderPlayer',
        ]);

        return $this->inertiaRender('BanAppeals/BanAppealShow', [
            'banAppeal' => new BanAppealResource($banAppeal),
        ]);
    }

    public function update(
        UpdateBanAppeal $useCase,
        BanAppealUpdateRequest $request,
        BanAppeal $banAppeal,
    ) {
        $this->requires(WebReviewPermission::BAN_APPEALS_DECIDE);

        $validated = $request->validated();

        try {
            $useCase->execute(
                banAppeal: $banAppeal,
                decidingAccount: $request->user(),
                decisionNote: $validated['decision_note'],
                status: BanAppealStatus::from($validated['status']),
            );
        } catch (NotImplementedException $e) {
            throw ValidationException::withMessages([
                'error' => ['This unban decision is not supported currently. Please contact an admin.'],
            ]);
        } catch (BanNotFoundException $e) {
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

        return to_route('review.ban-appeals.show', $banAppeal)
            ->with(['success' => 'Ban appeal updated and closed']);
    }
}
