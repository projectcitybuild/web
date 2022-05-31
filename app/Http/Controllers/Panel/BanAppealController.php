<?php

namespace App\Http\Controllers\Panel;

use App\Http\Requests\BanAppealUpdateRequest;
use Domain\BanAppeals\Entities\BanAppealStatus;
use Domain\BanAppeals\UseCases\UpdateBanAppealUseCase;
use Entities\Models\Eloquent\BanAppeal;
use Entities\Notifications\BanAppealUpdatedNotification;
use Illuminate\Validation\ValidationException;

class BanAppealController
{
    public function index()
    {
        $banAppeals = BanAppeal::paginate(50);

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
        $decidingPlayer = $request->user()->minecraftAccount()->first();

        if ($decidingPlayer == null) {
            throw ValidationException::withMessages([
                'error' => ['Can\'t perform ban actions with no minecraft accounts linked.']
            ]);
        }

        $useCase->execute(
            banAppeal: $banAppeal,
            decidingPlayer: $decidingPlayer,
            decisionNote: $request->get('decision_note'),
            status: BanAppealStatus::from($request->get('status'))
        );

        $banAppeal->notify(new BanAppealUpdatedNotification($banAppeal->showLink()));
    }

}
