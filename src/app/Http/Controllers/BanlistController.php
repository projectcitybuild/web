<?php

namespace App\Http\Controllers;

use App\Entities\Bans\Models\GameBan;
use App\Http\WebController;
use Illuminate\Http\Request;

class BanlistController extends WebController
{
    public function index()
    {
        $bans = GameBan::where('is_active', 1)->latest()->paginate(50);

        return view('front.pages.banlist')->with(compact('bans'));
    }
}
