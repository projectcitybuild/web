<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\WebController;
use Entities\Models\Eloquent\Page;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PageController extends WebController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pages = Page::get();

        return view('admin.page.index')->with(compact('pages'));
    }

    /**
     * Show the form for creating the specified resource.
     *
     * @return Application|Factory|View
     */
    public function create(Request $request)
    {
        $page = new Page();

        return view('admin.page.create')->with(compact('page'));
    }

    /**
     * Add a specified resource in storage.
     *
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'url' => 'required|alpha_dash|unique:pages,url',
            'description' => 'required',
            'contents' => 'required',
            'is_draft' => 'integer',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Page::create([
            'title' => $request->get('title'),
            'url' => $request->get('url'),
            'description' => $request->get('description'),
            'contents' => $request->get('contents'),
            'is_draft' => $request->get('is_draft') ?? 0,
        ]);

        return redirect(route('front.panel.pages.index'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return Application|Factory|View
     */
    public function edit(Page $page)
    {
        return view('admin.page.edit')->with(compact('page'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @return RedirectResponse
     */
    public function update(Request $request, Page $page)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'url' => ['required', 'alpha_dash', Rule::unique('pages', 'title')->ignore($page->getKey(), 'page_id')],
            'description' => 'required',
            'contents' => 'required',
            'is_draft' => 'integer',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $attributes = $request->all();
        if (! $request->has('is_draft')) {
            $attributes['is_draft'] = false;
        }

        $page->update($attributes);
        $page->save();

        return redirect(route('front.panel.pages.edit', $page))
            ->with('success', 'Page saved successfully');
    }

    /**
     * Delete the specified resource in storage.
     *
     * @return Application|Redirector|RedirectResponse
     */
    public function destroy(Request $request, Page $page)
    {
        $page->delete();

        return redirect(route('front.panel.pages.index'));
    }
}
