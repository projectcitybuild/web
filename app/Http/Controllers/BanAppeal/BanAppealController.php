<?php

namespace App\Http\Controllers\BanAppeal;

use App\Http\Requests\StoreBanAppealRequest;
use App\Http\WebController;
use Domain\BanAppeals\Entities\BanAppealStatus;
use Domain\BanAppeals\Exceptions\EmailRequiredException;
use Domain\BanAppeals\UseCases\CreateBanAppealUseCase;
use Entities\Models\Eloquent\BanAppeal;
use Entities\Models\Eloquent\GameBan;
use Entities\Models\Eloquent\MinecraftPlayer;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class BanAppealController extends WebController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $bans = $request->user()?->gameBans()
                ->with(['banAppeals', 'staffPlayer.aliases', 'bannedPlayer' => function (MorphTo $morphTo) {
                    $morphTo->morphWith([
                        MinecraftPlayer::class => ['aliases']
                    ]);
                }])->latest()->get() ?? collect();

        return view('v2.front.pages.ban-appeal.index')->with([
            'bans' => $bans,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(GameBan $ban, Request $request, CreateBanAppealUseCase $useCase)
    {
        if (!$ban->is_active) {
            return abort(404);
        }

        $existingAppeal = $ban->banAppeals()->pending()->first();
        if ($existingAppeal) {
            return view('v2.front.pages.ban-appeal.error-pending')->with([
                'existingAppeal' => $existingAppeal
            ]);
        }

        $bannedPlayer = $ban->bannedPlayer;
        $banHistory = $bannedPlayer->gameBans()
            ->with(['staffPlayer.aliases', 'bannedPlayer' => function (MorphTo $morphTo) {
                $morphTo->morphWith([
                    MinecraftPlayer::class => ['aliases']
                ]);
            }])
            ->latest()->get();

        return view('v2.front.pages.ban-appeal.create')->with([
            'player' => $bannedPlayer,
            'activeGameBan' => $ban,
            'banHistory' => $banHistory,
            'accountVerified' => $useCase->isAccountVerified($ban, $request->user())
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param GameBan $ban
     * @param StoreBanAppealRequest $request
     * @param CreateBanAppealUseCase $useCase
     * @return Response
     */
    public function store(GameBan $ban, StoreBanAppealRequest $request, CreateBanAppealUseCase $useCase)
    {
        try {
            $banAppeal = $useCase->execute($ban, $request->get('explanation'), $request->user(), $request->get('email'));
        } catch (EmailRequiredException $e) {
            $e->throwAsValidationException();
        }

        return redirect($banAppeal->showLink());
    }

    /**
     * Display the specified resource.
     *
     * @param BanAppeal $banAppeal
     * @param Request $request
     * @return Response
     * @throws AuthorizationException
     */
    public function show(BanAppeal $banAppeal, Request $request)
    {
        if (!$request->hasValidSignature()) {
            $this->authorize('view', $banAppeal);
        }

        return view('v2.front.pages.ban-appeal.show')->with([
            'banAppeal' => $banAppeal
        ]);
    }
}
