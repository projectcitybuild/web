<?php

namespace App\Http\Controllers;

use App\Http\WebController;
use Entities\Models\Eloquent\Page;
use Illuminate\Http\Request;

final class PageController extends WebController
{
    public function index(Request $request, string $name)
    {
        $page = Page::where('name', $name)->first();

        if ($page === null) {
            abort(404);
        }

        return view('v2.front.pages.page', ['page' => $page]);
    }
}
