<?php

namespace App\Http\Controllers\Front\BanAppeal;

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


}
