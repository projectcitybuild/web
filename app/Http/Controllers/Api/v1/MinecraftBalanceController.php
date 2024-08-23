<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

final class MinecraftBalanceController extends ApiController
{
    /**
     * @deprecated
     */
    public function show(
        Request $request,
        string $uuid,
    ) {
        return [
            'data' => ['balance' => 0],
        ];
    }

    /**
     * @deprecated
     */
    public function deduct(
        Request $request,
        string $uuid,
    ) {
        return [
            'data' => ['success' => false],
        ];
    }
}
