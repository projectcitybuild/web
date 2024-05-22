<?php

namespace App\Http\Controllers\Minecraft\Players;

use App\Http\Controllers\Controller;
use App\Models\Eloquent\Player;
use App\Models\MinecraftUUID;
use App\Models\Rules\EmailValidationRules;
use App\Models\Rules\PasswordValidationRules;
use App\Models\Rules\UsernameValidationRules;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class MinecraftAccountSyncController extends Controller
{
    use PasswordValidationRules;
    use UsernameValidationRules;
    use EmailValidationRules;

    public function show(Request $request, MinecraftUUID $uuid): JsonResponse
    {
        $player = Player::uuid($uuid)
            ->with(['mute'])
            ->firstOrFail();

        return response()->json($player);
    }

    public function store(Request $request, MinecraftUUID $uuid): JsonResponse
    {
        $validated = $request->validate([
            'username' => $this->usernameRules(),
            'email' => $this->emailRules(),
            'password' => $this->passwordRules(),
        ]);
        $validated = collect($validated);

        $email = $validated->get('email');

        // TODO: use minecraft_auth_code?

//        $account = Account::where('email', $email)->first();
//        if ($account === null) {
//            $account = Account::create([
//                'username' => $validated->get('username'),
//                'email' => $email,
//                'password' => Hash::make($validated->get('password')),
//            ]);
//        }

        // TODO: email code

        return response()->json();
    }
}
