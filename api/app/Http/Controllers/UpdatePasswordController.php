<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UpdatePasswordController extends Controller
{
    public function update(Request $request): JsonResponse
    {
        $account = $request->user();

        $request->validate([
            'new_password' => ['required', 'confirmed', Password::defaults()],
            'current_password' => ['required', Password::defaults(), function ($attribute, $value, $fail) use ($account) {
                if (!Hash::check($value, $account->password)) {
                    return $fail(__('The current password is incorrect.'));
                }
                return null;
            }],
        ]);

        $account->password = Hash::make($request->get('new_password'));
        $account->save();

        return response()->json();
    }
}
