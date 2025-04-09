<?php

namespace App\Http\Controllers\Front\BanAppeal;

use App\Http\Controllers\WebController;
use App\Models\BanAppeal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class BanAppealController extends WebController
{
    public function show(BanAppeal $banAppeal, Request $request)
    {
        // Allow access via magic link, otherwise authenticate with policy
        if (! $request->hasValidSignature()) {
            Gate::authorize('view', $banAppeal);
        }
        return view('front.pages.ban-appeal.show', compact('banAppeal'));
    }
}
