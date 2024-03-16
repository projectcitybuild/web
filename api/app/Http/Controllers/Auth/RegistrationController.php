<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegistrationRequest;
use App\Models\Eloquent\Account;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class RegistrationController extends Controller
{
    public function __invoke(RegistrationRequest $request): JsonResponse
    {
        $validated = $request->safe()->collect();

        $password = Hash::make($validated->get('password'));

        $account = Account::create([
            'username' => $validated->get('username'),
            'email' => $validated->get('email'),
            'password' => $password,
        ]);

        // Email sending handled by Laravel
        // https://laravel.com/docs/11.x/verification
        event(new Registered($account));

        return response()->json();
    }
}
