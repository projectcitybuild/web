<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RobThree\Auth\TwoFactorAuth;

class TwoFactorRecoveryController extends Controller
{
    public function __invoke(Request $request, TwoFactorAuth $twoFactorAuth): JsonResponse
    {
        // TODO
        return response()->json();
    }
}
