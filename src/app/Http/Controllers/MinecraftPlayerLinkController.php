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
            abort(400, 'Invalid or expired token. Please restart the authentication process');
        }

        // We can't associate a PCB account with a Minecraft player if they're not logged in.
        // Force them through the login flow and then redirect them back here after they're done
        if (Auth::check() === false || Auth::id() === null) {
            return redirect()->route('front.login', [
                'return_url' => url()->full(),
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
