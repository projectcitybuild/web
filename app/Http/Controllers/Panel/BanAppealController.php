<?php

namespace App\Http\Controllers\Panel;

use App\Exceptions\Http\NotImplementedException;
use App\Http\Requests\BanAppealUpdateRequest;
use Domain\BanAppeals\Entities\BanAppealStatus;
use Domain\BanAppeals\Exceptions\AppealAlreadyDecidedException;
use Domain\BanAppeals\UseCases\UpdateBanAppealUseCase;
use Domain\Bans\Exceptions\PlayerNotBannedException;
use Entities\Models\Eloquent\BanAppeal;
use Entities\Notifications\BanAppealUpdatedNotification;
use Illuminate\Validation\ValidationException;
use Repositories\BanAppealRepository;

class BanAppealController
{
    public function index(BanAppealRepository $banAppealRepository)
    {
        $banAppeals = $banAppealRepository->allWithPriority(50);

        return view('admin.ban-appeal.index')->with([
            'banAppeals' => $banAppeals
        ]);
    }

    public function show(BanAppeal $banAppeal)
    {
        return view('admin.ban-appeal.show')->with([
            'banAppeal' => $banAppeal
        ]);
    }

    public function update(UpdateBanAppealUseCase $useCase, BanAppealUpdateRequest $request, BanAppeal $banAppeal)
    {
        try {
            $useCase->execute(
                banAppeal: $banAppeal,
                decidingPlayer: $request->user()->minecraftAccount()->first(),
                decisionNote: $request->get('decision_note'),
                status: BanAppealStatus::from($request->get('status'))
            );
        } catch (NotImplementedException $e) {
            throw ValidationException::withMessages([
                'error' => ['This unban decision is not supported currently. Please contact an admin.']
            ]);
        } catch (PlayerNotBannedException $e) {
            throw ValidationException::withMessages([
                'error' => ['Unable to unban player, they are not currently banned.']
            ]);
        } catch (AppealAlreadyDecidedException $e) {
            throw ValidationException::withMessages([
                'error' => ['This appeal has already been decided.']
            ]);
        }


        $banAppeal->notify(new BanAppealUpdatedNotification($banAppeal->showLink()));

        return redirect()->route('front.panel.ban-appeals.show', $banAppeal);
    }
}
