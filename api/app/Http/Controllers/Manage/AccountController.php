<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\UnauthorizedException;

class AccountController extends Controller
{
    /**
     * @throws UnauthorizedException if not logged in
     */
    public function me(Request $request): JsonResponse
    {
        $account = $request->user();

        if ($account === null) {
            throw new UnauthorizedException();
        }
        return response()->json($account);
    }
}
