<?php

namespace App\Http\Controllers\Api\v1;

use App\Exceptions\Http\BadRequestException;
use App\Http\ApiController;
use Domain\Balances\Exceptions\InsufficientBalanceException;
use Domain\Balances\UseCases\DeductBalanceUseCase;
use Domain\Balances\UseCases\GetBalanceUseCase;
use Illuminate\Http\Request;
use Shared\PlayerLookup\Entities\PlayerIdentifier;
use Shared\PlayerLookup\Exceptions\InvalidMinecraftUUIDException;
use Shared\PlayerLookup\Exceptions\NoLinkedAccountException;
use Shared\PlayerLookup\Exceptions\PlayerNotFoundException;

final class MinecraftBalanceController extends ApiController
{
    public function show(
        Request $request,
        string $uuid,
        GetBalanceUseCase $getBalance,
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
        DeductBalanceUseCase $deductBalance,
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
