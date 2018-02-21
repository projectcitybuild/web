<?php

namespace App\Routes\Http\Web\Controllers;

use App\Routes\Http\Web\WebController;
use Illuminate\Validation\Factory as Validation;

class LoginController extends WebController {

    public function showLoginView() {
        return view('login');
    }

    public function login(Request $request, Validation $validation) {
        $validator = $validation->make($request->all(), [

        ]);
    }

}
