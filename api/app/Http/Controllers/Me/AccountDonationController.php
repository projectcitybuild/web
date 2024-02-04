<?php

namespace App\Http\Controllers\Me;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AccountDonationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $account = $request->user();
        $donations = $account->donations ?? [];

        return response()->json($donations);
    }
}
