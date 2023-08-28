<?php

namespace App\Http\Controllers\Donations;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DonationPerkController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $account = $request->user();
        $perks = $account->perks ?? [];

        return response()->json($perks);
    }
}
