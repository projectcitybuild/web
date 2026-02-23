<?php

namespace App\Http\Controllers\Front;

use App\Models\MinecraftPlayerSession;
use Illuminate\Http\Request;

class HomeController
{
    public function index(Request $request)
    {
        // TODO: cache
        $sessions = MinecraftPlayerSession::orderBy('starts_at', 'desc')
            ->with('player.account.roles')
            ->limit(16)
            ->get();

        return view('front.pages.home.index', compact('sessions'));
    }
}
