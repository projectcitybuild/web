<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\WebController;
use Entities\Models\Eloquent\MinecraftAuthCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

final class MinecraftLinkController extends WebController
{
    public function update(Request $request, string $token)
    {
        $authCode = MinecraftAuthCode::where('token', $token)->first();

        if ($authCode === null) {
            // TODO: throw?
            return response()->json([
                'error' => 'Invalid or expired token. Please restart the authentication process from in-game',
            ], 405); // TODO: use correct status
        }

        DB::beginTransaction();
        try {
            $minecraftPlayer = $authCode->minecraftPlayer;
            $minecraftPlayer->account_id = Auth::id();
            $minecraftPlayer->save();

            // Prevent reuse of the same authentication token
            $authCode->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return response()->json([]);
    }
}
