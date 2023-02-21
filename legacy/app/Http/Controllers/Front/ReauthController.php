<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\WebController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ReauthController extends WebController
{
    /**
     * Handle the incoming request.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        return view('front.pages.login.reauth');
    }

    public function process(Request $request)
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
