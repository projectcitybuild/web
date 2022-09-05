<?php

namespace App\Http\Controllers;

use App\Http\WebController;
use Entities\Models\Eloquent\Page;
use Illuminate\Http\Request;

final class PageController extends WebController
{
    public function index(Request $request, string $url)
    {
        $page = Page::where('url', $url)->first();

        if ($page === null || $page->is_draft) {
            abort(404);
        }

        return view('front.pages.page', ['page' => $page]);
    }
}
