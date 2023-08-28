<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Fortify\CreateNewUser;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RegistrationController extends Controller
{
    public function register(Request $request, CreateNewUser $createNewUser): JsonResponse
    {
        $account = $createNewUser->create($request->all());

        event(new Registered($account));

        return response()->json();
    }
}
