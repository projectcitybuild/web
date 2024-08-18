<?php

namespace App\Http\Controllers\API\v1;

use App\Core\Exceptions\BadRequestException;
use App\Http\Controllers\APIController;
use Domain\Balances\Exceptions\InsufficientBalanceException;
use Domain\Balances\UseCases\DeductBalance;
use Domain\Balances\UseCases\GetBalance;
use Illuminate\Http\Request;
use Shared\PlayerLookup\Entities\PlayerIdentifier;
use Shared\PlayerLookup\Exceptions\InvalidMinecraftUUIDException;
use Shared\PlayerLookup\Exceptions\NoLinkedAccountException;
use Shared\PlayerLookup\Exceptions\PlayerNotFoundException;

final class MinecraftBalanceController extends APIController
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
