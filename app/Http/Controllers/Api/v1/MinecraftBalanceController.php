<?php

namespace App\Http\Controllers\Api\v1;

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
        return [
            'balance' => $getBalanceUseCase->execute(uuid: $uuid),
        ];
    }

    public function deduct(Request $request)
    {
        $this->validateRequest(
            requestData: [],
            rules: [],
            messages: [],
        );
    }
}
