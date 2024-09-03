<?php

namespace App\Http\Controllers\Front\Auth;

use App\Http\Controllers\WebController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ReauthController extends WebController
{
    public function show(Request $request)
    {
        return view('front.pages.auth.reauth');
    }

    public function store(Request $request)
    {
        if (! Hash::check($request->password, $request->user()->password)) {
            return back()->withErrors([
                'password' => ['The provided password does not match our records.'],
            ]);
        }

        $request->session()->passwordConfirmed();

        return redirect()->intended();
    }
}
