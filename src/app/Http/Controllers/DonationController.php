<?php

namespace App\Http\Controllers;

use App\Http\WebController;

final class DonationController extends WebController
{
    public function index()
    {
        return view('front.pages.donate.donate');
    }
}
