<?php

namespace App\Http\Controllers\Front\BanAppeal;

use App\Domains\BanAppeals\UseCases\CreateBanAppeal;
use App\Domains\Captcha\Rules\CaptchaRule;
use App\Http\Controllers\WebController;
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
        Request $request,
        CaptchaRule $captchaRule,
        CreateBanAppeal $useCase,
    ) {
        $validated = $request->validate([
            'captcha-response' => ['required', $captchaRule],
            'minecraft_uuid' => 'required',
            'date_of_ban' => 'required',
            'ban_id' => ['nullable', 'integer'],
            'email' => ['required', 'email'],
            'ban_reason' => 'required',
            'unban_reason' => 'required',
        ]);
        $banAppeal = $useCase->execute(
            banId: $validated['ban_id'],
            email: $validated['email'],
            minecraftUuid: $validated['minecraft_uuid'],
            dateOfBan: $validated['date_of_ban'],
            banReason: $validated['ban_reason'],
            unbanReason: $validated['unban_reason'],
            account: $request->user(),
        );
        return redirect($banAppeal->showLink());
    }
}
