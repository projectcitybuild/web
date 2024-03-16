<?php

namespace App\Http\Controllers\Me;

use App\Actions\Me\UpdateUserPassword;
use App\Http\Controllers\Controller;
use App\Http\Requests\Me\UpdatePasswordRequest;
use Illuminate\Http\JsonResponse;

class UpdatePasswordController extends Controller
{
    public function update(UpdatePasswordRequest $request, UpdateUserPassword $updatePassword): JsonResponse
    {
        $validated = $request->safe()->collect();

        $updatePassword->update(
            user: $request->user(),
            newPassword: $validated->get('password'),
        );

        return response()->json();
    }
}
