<?php

namespace App\Http\Controllers\Api\v1;

use App\Exceptions\Http\BadRequestException;
use App\Http\ApiController;
use Domain\Balances\Exceptions\InsufficientBalanceException;
use Domain\Balances\UseCases\DeductBalanceUseCase;
use Domain\Balances\UseCases\GetBalanceUseCase;
use Illuminate\Http\Request;
use Shared\PlayerLookup\Entities\PlayerIdentifier;
use Shared\PlayerLookup\Exceptions\NoLinkedAccountException;
use Shared\PlayerLookup\Exceptions\PlayerNotFoundException;

final class MinecraftBalanceController extends ApiController
{
    /**
     * @throws NoLinkedAccountException
     * @throws PlayerNotFoundException
     */
    public function show(
        Request $request,
        string $uuid,
        GetBalanceUseCase $getBalanceUseCase,
    ) {
        $balance = $getBalanceUseCase->execute(
            identifier: PlayerIdentifier::minecraftUUID($uuid),
        );
        return [
            'balance' => $balance,
        ];
    }

    /**
     * @throws NoLinkedAccountException
     * @throws BadRequestException
     * @throws PlayerNotFoundException
     * @throws InsufficientBalanceException
     */
    public function deduct(
        Request $request,
        string $uuid,
        DeductBalanceUseCase $deductBalanceUseCase,
    ) {
        $this->validateRequest(
            requestData: $request->all(),
            rules: [
                'amount' => 'required|int|gt:0',
                'reason' => 'required|string',
            ],
        );
        $deductBalanceUseCase->execute(
            identifier: PlayerIdentifier::minecraftUUID($uuid),
            amount: $request->get('amount'),
            reason: $request->get('reason'),
        );
    }
}
