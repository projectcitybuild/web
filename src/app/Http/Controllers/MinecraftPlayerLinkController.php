<?php

namespace App\Http\Controllers;

use App\Http\WebController;
use Illuminate\Http\Request;
use App\Entities\Players\Repositories\MinecraftAuthCodeRepository;
use Illuminate\Support\Facades\Auth;

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

        if ($authCode === null) {
            abort(400, 'Invalid or expired token. Please restart the authentication process');
        }

        if (Auth::check() === false) {
            // $redirectUrl = 
        }

        return 'TODO';
    }
}
