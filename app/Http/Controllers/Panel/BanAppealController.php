<?php

namespace App\Http\Controllers\Panel;

use App\Core\Data\Exceptions\NotImplementedException;
use App\Domains\BanAppeals\Entities\BanAppealStatus;
use App\Domains\BanAppeals\Exceptions\AppealAlreadyDecidedException;
use App\Domains\BanAppeals\Notifications\BanAppealUpdatedNotification;
use App\Domains\BanAppeals\UseCases\UpdateBanAppeal;
use App\Domains\Bans\Exceptions\NotBannedException;
use App\Domains\Panel\Exceptions\NoPlayerForActionException;
use App\Http\Requests\BanAppealUpdateRequest;
use App\Models\BanAppeal;
use Illuminate\Validation\ValidationException;

class BanAppealController
{
    public function index()
    {
        // Get ban appeals paginated in the order:
        // Pending appeal (newest first), then all other appeals (newest first)
        $banAppeals = BanAppeal::orderByRaw('FIELD(status, '.BanAppealStatus::PENDING->value.') DESC')
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('admin.ban-appeal.index')->with([
            'banAppeals' => $banAppeals,
        ]);
    }

    public function show(BanAppeal $banAppeal)
    {
        return view('admin.ban-appeal.show')->with([
            'banAppeal' => $banAppeal,
        ]);
    }

    public function update(UpdateBanAppeal $useCase, BanAppealUpdateRequest $request, BanAppeal $banAppeal)
    {
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

        return redirect()->route('front.panel.ban-appeals.show', $banAppeal);
    }
}
