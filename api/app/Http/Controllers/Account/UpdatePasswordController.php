<?php

namespace App\Http\Controllers\Account;

use App\Actions\Fortify\UpdateUserPassword;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UpdatePasswordController extends Controller
{
    public function update(Request $request, UpdateUserPassword $updater): JsonResponse
    {
        $updater->update(
            user: $request->user(),
            input: $request->all(),
        );
        return response()->json();
    }
}
