<?php

namespace App\Http\Controllers\Gallery;

use App\Http\WebController;
use Illuminate\Http\Request;

final class GalleryController extends WebController
{
    public function index(Request $request)
    {
        return view('v2.front.pages.gallery.gallery-front');
    }
}
