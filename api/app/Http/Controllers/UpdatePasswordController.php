<?php

namespace App\Http\Controllers;

use App\Actions\Fortify\UpdateUserPassword;
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
