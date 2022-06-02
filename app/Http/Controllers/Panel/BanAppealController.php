<?php

namespace App\Http\Controllers\Panel;

use App\Http\Requests\BanAppealUpdateRequest;
use Domain\BanAppeals\Entities\BanAppealStatus;
use Domain\BanAppeals\Repositories\BanAppealRepository;
use Domain\BanAppeals\UseCases\UpdateBanAppealUseCase;
use Entities\Models\Eloquent\BanAppeal;
use Entities\Notifications\BanAppealUpdatedNotification;

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
        $useCase->execute(
            banAppeal: $banAppeal,
            decidingPlayer: $request->user()->minecraftAccount()->first(),
            decisionNote: $request->get('decision_note'),
            status: BanAppealStatus::from($request->get('status'))
        );

        $banAppeal->notify(new BanAppealUpdatedNotification($banAppeal->showLink()));

        return redirect()->route('front.panel.ban-appeals.show', $banAppeal);
    }
}
