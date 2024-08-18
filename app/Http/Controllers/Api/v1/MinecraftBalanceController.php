<?php

namespace App\Http\Controllers\Api\v1;

use App\Core\Data\Exceptions\BadRequestException;
use App\Core\Domains\PlayerLookup\Data\PlayerIdentifier;
use App\Core\Domains\PlayerLookup\Exceptions\InvalidMinecraftUUIDException;
use App\Core\Domains\PlayerLookup\Exceptions\NoLinkedAccountException;
use App\Core\Domains\PlayerLookup\Exceptions\PlayerNotFoundException;
use App\Domains\Balances\Exceptions\InsufficientBalanceException;
use App\Domains\Balances\UseCases\DeductBalance;
use App\Domains\Balances\UseCases\GetBalance;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

final class MinecraftBalanceController extends ApiController
{
    public function show(
        Request $request,
        string $uuid,
        GetBalance $getBalance,
    ) {
        try {
            $balance = $getBalance->execute(
                identifier: PlayerIdentifier::minecraftUUID($uuid),
            );

            return [
                'data' => ['balance' => $balance],
            ];
        } catch (NoLinkedAccountException | PlayerNotFoundException) {
            return [
                'data' => ['balance' => 0],
            ];
        }
    }

    /**
     * @throws NoLinkedAccountException
     * @throws BadRequestException
     * @throws PlayerNotFoundException
     * @throws InsufficientBalanceException|InvalidMinecraftUUIDException
     */
    public function deduct(
        Request $request,
        string $uuid,
        DeductBalance $deductBalance,
    ) {
        $this->validateRequest(
            requestData: $request->all(),
            rules: [
                'amount' => 'required|int|gt:0',
                'reason' => 'required|string',
            ],
        );
        $deductBalance->execute(
            identifier: PlayerIdentifier::minecraftUUID($uuid),
            amount: $request->get('amount'),
            reason: $request->get('reason'),
        );

        return [
            'data' => ['success' => true],
        ];
    }
}
