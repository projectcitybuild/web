<?php

namespace App\Http\Controllers;

use App\Http\WebController;
use Illuminate\Support\Facades\Auth;

final class DonationController extends WebController
{
    public function index()
    {
        return view('front.pages.donate.donate');
    }

    public function success()
    {
        return view('front.pages.donate.donate-thanks');
    }
}
