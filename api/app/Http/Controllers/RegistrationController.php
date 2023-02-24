<?php

namespace App\Http\Controllers;

use App\Actions\Fortify\CreateNewUser;
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
