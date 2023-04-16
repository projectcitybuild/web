<?php

namespace App\Http\Controllers;

use App\Exceptions\Http\UnauthorisedException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    /**
     * @throws UnauthorisedException if not logged in
     */
    public function me(Request $request): JsonResponse
    {
        $account = auth()->user();

        if ($account === null) {
            throw new UnauthorisedException();
        }
        return response()->json($account);
    }
}
