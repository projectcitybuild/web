<?php

namespace App\Http\Controllers\Front\BanAppeal;

use App\Core\Data\Exceptions\TooManyRequestsException;
use App\Core\Domains\PlayerLookup\Exceptions\PlayerNotFoundException;
use App\Domains\Bans\Exceptions\NotBannedException;
use App\Domains\Bans\UseCases\LookupPlayerBan;
use App\Http\Controllers\WebController;
use App\Http\Requests\BanLookupRequest;
use Illuminate\Validation\ValidationException;

class BanLookupController extends WebController
{
    public function __invoke(BanLookupRequest $request, LookupPlayerBan $useCase)
    {
        try {
            $ban = $useCase->execute($request->get('username'));

            return redirect()->route('front.appeal.create', $ban);
        } catch (TooManyRequestsException) {
            throw ValidationException::withMessages([
                'error' => ['The Mojang API is too busy currently. Please try again later'],
            ]);
        } catch (NotBannedException) {
            throw ValidationException::withMessages([
                'error' => ['This player has no active bans.'],
            ]);
        } catch (PlayerNotFoundException) {
            throw ValidationException::withMessages([
                'error' => ['This username does not belong to a Minecraft player. Check you have entered it correctly.'],
            ]);
        }
    }
}
