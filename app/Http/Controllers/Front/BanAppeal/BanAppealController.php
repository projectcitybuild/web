<?php

namespace App\Http\Controllers\Front\BanAppeal;

use App\Http\Controllers\WebController;
use App\Http\Requests\StoreBanAppealRequest;
use Domain\BanAppeals\Exceptions\EmailRequiredException;
use Domain\BanAppeals\UseCases\CreateBanAppeal;
use Entities\Models\Eloquent\BanAppeal;
use Entities\Models\Eloquent\GamePlayerBan;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BanAppealController extends WebController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $bans = $request->user()?->gamePlayerBans()
                ->with(['banAppeals', 'staffPlayer.aliases', 'bannedPlayer.aliases'])
                ->latest()->get() ?? collect();

        return view('front.pages.ban-appeal.index')->with([
            'bans' => $bans,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(GamePlayerBan $ban, Request $request, CreateBanAppeal $useCase)
    {
        if (! $ban->isActive()) {
            return abort(404);
        }

        $existingAppeal = $ban->banAppeals()->pending()->first();
        if ($existingAppeal) {
            return view('front.pages.ban-appeal.error-pending')->with([
                'existingAppeal' => $existingAppeal,
            ]);
        }

        $bannedPlayer = $ban->bannedPlayer;
        $banHistory = $bannedPlayer->gamePlayerBans()
            ->with(['staffPlayer.aliases', 'bannedPlayer.aliases'])
            ->latest()->get();

        return view('front.pages.ban-appeal.create')->with([
            'player' => $bannedPlayer,
            'activegamePlayerBan' => $ban,
            'banHistory' => $banHistory,
            'accountVerified' => $useCase->isAccountVerified($ban, $request->user()),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  GamePlayerBan  $ban
     * @param  StoreBanAppealRequest  $request
     * @param  CreateBanAppeal  $useCase
     * @return Response
     */
    public function store(GamePlayerBan $ban, StoreBanAppealRequest $request, CreateBanAppeal $useCase)
    {
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

    /**
     * Display the specified resource.
     *
     * @param  BanAppeal  $banAppeal
     * @param  Request  $request
     * @return Response
     *
     * @throws AuthorizationException
     */
    public function show(BanAppeal $banAppeal, Request $request)
    {
        if (! $request->hasValidSignature()) {
            $this->authorize('view', $banAppeal);
        }

        return view('front.pages.ban-appeal.show')->with([
            'banAppeal' => $banAppeal,
        ]);
    }
}
