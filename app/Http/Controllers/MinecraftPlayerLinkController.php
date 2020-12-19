<?php

namespace App\Http\Controllers;

use App\Entities\Players\Models\MinecraftAuthCode;
use App\Http\WebController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

final class MinecraftPlayerLinkController extends WebController
{
    public function index(Request $request, string $token)
    {
        $authCode = MinecraftAuthCode::where('token', $token)->first();

        if ($authCode === null) {
            return view('front.pages.minecraft-auth.error', [
                'message' => 'Invalid or expired token. Please restart the authentication process from in-game',
            ]);
        }

        DB::beginTransaction();
        try {
            $minecraftPlayer = $authCode->minecraftPlayer;
            $minecraftPlayer->account_id = Auth::id();
            $minecraftPlayer->save();

            // Prevent reuse of the same authentication token
            MinecraftAuthCode::where('minecraft_auth_code_id', $authCode->getKey())->delete();

            DB::commit();
        }
        catch(\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return view('front.pages.minecraft-auth.complete');
    }
}
