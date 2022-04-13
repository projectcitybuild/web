<?php

namespace App\Http\Controllers\Api\v1;

use App\Entities\Models\GameIdentifierType;
use App\Http\ApiController;
use Domain\Balances\UseCases\GetBalanceUseCase;
use Illuminate\Http\Request;

final class MinecraftBalanceController extends ApiController
{
    public function show(
        Request $request,
        string $uuid,
        GetBalanceUseCase $getBalanceUseCase,
    ) {
        $balance = $getBalanceUseCase->execute(
            identifier: $uuid,
            identifierType: GameIdentifierType::MINECRAFT_UUID,
        );
        return [
            'balance' => $balance,
        ];
    }

    public function deduct(Request $request)
    {
        $this->validateRequest(
            requestData: $request->all(),
            rules: [
                'amount' => 'required|int',
                'reason' => 'required|string',
            ],
            messages: [],
        );
    }
}
