<?php

namespace App\Http\Controllers\Api\v1;

use App\Exceptions\Http\BadRequestException;
use App\Http\ApiController;
use Domain\Balances\Exceptions\InsufficientBalanceException;
use Domain\Balances\UseCases\DeductBalanceUseCase;
use Domain\Balances\UseCases\GetBalanceUseCase;
use Entities\Models\Eloquent\Badge;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Shared\PlayerLookup\Entities\PlayerIdentifier;
use Shared\PlayerLookup\Exceptions\InvalidMinecraftUUIDException;
use Shared\PlayerLookup\Exceptions\NoLinkedAccountException;
use Shared\PlayerLookup\Exceptions\PlayerNotFoundException;
use Shared\PlayerLookup\PlayerLookup;

final class MinecraftBadgeController extends ApiController
{
    public function show(
        Request $request,
        string $uuid,
        PlayerLookup $playerLookup,
    ) {
        $account = $playerLookup
            ->find(identifier: PlayerIdentifier::minecraftUUID($uuid))
            ?->getLinkedAccount();

        if ($account === null) {
            return ['data' => []];
        }

        $badges = $account->badges;
        $accountAgeInYears = $account->created_at->diffInYears(now());

        if ($accountAgeInYears >= 3) {
            $badges->add(Badge::make([
                'display_name' => $accountAgeInYears . ' years on PCB',
                'unicode_icon' => '❤',
            ]));
        }

        return [
            'data' => $badges,
        ];
    }
}
