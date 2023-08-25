<?php

namespace App\Http\Controllers\Donations;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\UnauthorizedException;
use App\Models\Eloquent\Donation;

class DonationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $account = $request->user();
        $donations = $account->donations;

        return response()->json($donations);
    }
}
