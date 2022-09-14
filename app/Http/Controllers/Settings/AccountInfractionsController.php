<?php

namespace App\Http\Controllers\Settings;

use App\Http\WebController;
use Domain\Warnings\UseCases\AcknowledgeWarning;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

final class AccountInfractionsController extends WebController
{
    public function index(Request $request): View
    {
        $account = $request->user();
        $account->load(['warnings', 'gameBans']);

        return view('front.pages.account.account-infractions')
            ->with(compact('account'));
    }

    public function acknowledgeWarning(
        Request $request,
        int $warningId,
        AcknowledgeWarning $acknowledgeWarning,
    ) {
        $acknowledgeWarning->execute(
            warningId: $warningId,
            accountId: $request->user()->getKey(),
        );
        return redirect()->back()->with(
            key: 'success',
            value: 'Warning acknowledged successfully',
        );
    }
}
