<?php

namespace App\Http\Controllers;

use App\Http\WebController;
use Illuminate\Http\Request;
use App\Entities\Players\Repositories\MinecraftAuthCodeRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

final class MinecraftPlayerLinkController extends WebController
{
    /**
     * @var MinecraftAuthCodeRepository
     */
    private $minecraftAuthCodeRepository;

    public function __construct(MinecraftAuthCodeRepository $minecraftAuthCodeRepository)
    {
        $this->minecraftAuthCodeRepository = $minecraftAuthCodeRepository;
    }

    public function linkMinecraftPlayerWithAccount(Request $request, string $token)
    {
        $authCode = $this->minecraftAuthCodeRepository->getByToken($token);

        if ($authCode === null) 
        {
            abort(400, 'Invalid or expired token. Please restart the authentication process');
        }

        // We can't associate a PCB account with a Minecraft player if they're not logged in.
        // Force them through the login flow and then redirect them back here after they're done
        if (Auth::check() === false || Auth::id() === null) 
        {
            return redirect()->route('front.login', [
                'return_url' => url()->full(),
            ]);
        }

        DB::beginTransaction();
        try 
        {
            $minecraftPlayer = $authCode->minecraftPlayer;
            $minecraftPlayer->account_id = Auth::id();
            $minecraftPlayer->save();

            // prevent reuse of the same authentication token
            $this->minecraftAuthCodeRepository->deleteById($authCode->getKey());

            DB::commit();
        }
        catch(\Exception $e) 
        {
            DB::rollBack();
            throw $e;
        }

        // TODO: show 'authentication complete' page - tell user to run `/auth sync` command
        return 'TODO';
    }
}
