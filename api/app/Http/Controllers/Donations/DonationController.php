<?php

namespace App\Http\Controllers\Donations;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DonationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $account = $request->user();
        $donations = $account->donations ?? [];

        return response()->json($donations);
    }
}
