<?php

namespace App\Http\Controllers\Gallery;

use App\Http\WebController;
use Entities\Models\Eloquent\Account;
use Illuminate\Http\Request;

final class GalleryFormController extends WebController
{
    public function index(Request $request)
    {
        return view('v2.front.pages.gallery.gallery-form');
    }

    public function store(Request $request)
    {
        // TODO: check if logged-in

        /** @var Account $account */
        $account = $request->account;

        $account->addMediaFromRequest('photo')
            ->toMediaCollection('gallery.images');

        return redirect()->route('front.gallery.form');
    }
}
