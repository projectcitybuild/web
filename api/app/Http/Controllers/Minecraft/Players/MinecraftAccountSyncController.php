<?php

namespace App\Http\Controllers\Minecraft\Players;

use App\Core\MinecraftUUID\MinecraftUUID;
use App\Domains\Accounts\Data\Rules\EmailValidationRules;
use App\Domains\Accounts\Data\Rules\PasswordValidationRules;
use App\Domains\Accounts\Data\Rules\UsernameValidationRules;
use App\Http\Controllers\Controller;
use App\Models\Player;
use App\Models\PlayerMute;
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
            ->firstOrFail();

        // TODO: convert this to a relationship later
        $mute = PlayerMute::forPlayer($player)->first();

        return response()->json([
            'player' => $player,
            'mute' => $mute,
        ]);
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
