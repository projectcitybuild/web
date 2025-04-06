<?php

namespace App\Http\Controllers\Front\BanAppeal;

use App\Http\Controllers\WebController;
use App\Models\BanAppeal;
use Illuminate\Http\Request;

class BanAppealController extends WebController
{
    public function show(BanAppeal $banAppeal, Request $request)
    {
        if (! $request->hasValidSignature()) {
            $this->authorize('view', $banAppeal);
        }

        return view('front.pages.ban-appeal.show', compact('banAppeal'));
    }
}
