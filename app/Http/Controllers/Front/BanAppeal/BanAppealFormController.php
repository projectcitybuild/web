<?php

namespace App\Http\Controllers\Front\BanAppeal;

use App\Domains\BanAppeals\Exceptions\EmailRequiredException;
use App\Domains\BanAppeals\UseCases\CreateBanAppeal;
use App\Http\Controllers\WebController;
use App\Http\Requests\StoreBanAppealRequest;
use App\Models\GamePlayerBan;
use Illuminate\Http\Request;

class BanAppealFormController extends WebController
{
    public function index(Request $request)
    {
        return view('front.pages.ban-appeal.form', ['ban' => null]);
    }

    public function show(Request $request, GamePlayerBan $ban)
    {
        return view('front.pages.ban-appeal.form', compact('ban'));
    }

    public function store(
        GamePlayerBan $ban,
        StoreBanAppealRequest $request,
        CreateBanAppeal $useCase,
    ) {
        try {
            $banAppeal = $useCase->execute(
                ban: $ban,
                explanation: $request->get('explanation'),
                loggedInAccount: $request->user(),
                email: $request->get('email'),
            );
        } catch (EmailRequiredException $e) {
            $e->throwAsValidationException();
        }

        return redirect($banAppeal->showLink());
    }
}
