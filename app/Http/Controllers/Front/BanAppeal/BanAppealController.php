<?php

namespace App\Http\Controllers\Front\BanAppeal;

use App\Domains\Permissions\AuthorizesPermissions;
use App\Domains\Permissions\WebReviewPermission;
use App\Http\Controllers\WebController;
use App\Models\BanAppeal;
use Illuminate\Http\Request;

class BanAppealController extends WebController
{
    use AuthorizesPermissions;

    public function show(BanAppeal $banAppeal, Request $request)
    {
        // Allow access via magic link, otherwise authorize normally
        if (
            ! $request->hasValidSignature() &&
            $request->user()?->getKey() !== $banAppeal->account_id &&
            ! $this->can(WebReviewPermission::BAN_APPEALS_VIEW)
        ) {
            abort(403);
        }
        return view('front.pages.ban-appeal.show', compact('banAppeal'));
    }
}
