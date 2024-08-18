<?php

namespace App\Http\Controllers\Front\BanAppeal;

use App\Core\Domains\RateLimit\Storage\SessionTokenStorage;
use App\Core\Domains\RateLimit\TokenBucket;
use App\Core\Domains\RateLimit\TokenRate;
use App\Core\Exceptions\TooManyRequestsException;
use App\Http\Controllers\WebController;
use App\Http\Requests\BanLookupRequest;
use Domain\Bans\Exceptions\NotBannedException;
use Domain\Bans\UseCases\LookupPlayerBan;
use Illuminate\Validation\ValidationException;
use Shared\PlayerLookup\Exceptions\PlayerNotFoundException;

class BanLookupController extends WebController
{
    public function __invoke(BanLookupRequest $request, LookupPlayerBan $useCase)
    {
        if (! $this->rateLimiter()->consume(1)) {
            throw ValidationException::withMessages([
                'error' => ['Too many lookup attempts. Please try again later.'],
            ]);
        }

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

    private function rateLimiter()
    {
        return new TokenBucket(
                capacity: 6,
                refillRate: TokenRate::refill(3)
                    ->every(2, TokenRate::MINUTES),
                storage: new SessionTokenStorage(
                    sessionName: 'banLookup.rate',
                    initialTokens: 3
                ),
            );
    }
}
