<?php

namespace App\Http\Controllers\Panel;

use App\Http\WebController;
use Entities\Models\Eloquent\ServerToken;
use Entities\Models\Eloquent\ServerTokenScope;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Library\Tokens\TokenGenerator;

class ServerTokenController extends WebController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Application|Factory|View
    {
        $tokens = ServerToken::orderby('created_at', 'desc')->paginate(100);

        return view('admin.server-tokens.index')->with(compact('tokens'));
    }

    /**
     * Show the form for creating the specified resource.
     */
    public function create(Request $request, TokenGenerator $tokenGenerator): Application|Factory|View
    {
        $token = new ServerToken();
        $generatedToken = $tokenGenerator->make();
        $scopes = ServerTokenScope::all();

        return view('admin.server-tokens.create')
            ->with(compact('token', 'generatedToken', 'scopes'));
    }

    /**
     * Add a specified resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
            'server_id' => 'nullable|numeric|exists:servers,server_id',
            'description' => 'string',
            'scopes' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $scopes = collect($request->get('scopes'))
            ->map(fn ($name) => ServerTokenScope::where('scope', $name)->first()?->getKey())
            ->filter()
            ->toArray();

        DB::beginTransaction();
        try {
            $token = ServerToken::create([
                'token' => $request->get('token'),
                'server_id' => $request->get('server_id'),
                'description' => $request->get('description'),
            ]);
            $token->scopes()->attach($scopes);
            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return redirect(route('front.panel.server-tokens.index'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $tokenId): Application|Factory|View
    {
        $token = ServerToken::with('scopes')->find($tokenId);
        $scopes = ServerTokenScope::all();

        return view('admin.server-tokens.edit')->with(compact('token', 'scopes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $tokenId): RedirectResponse
    {
        $token = ServerToken::find($tokenId);

        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
            'server_id' => 'nullable|numeric|exists:servers,server_id',
            'description' => 'string',
            'scopes' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $scopes = collect($request->get('scopes'))
            ->map(fn ($name) => ServerTokenScope::where('scope', $name)->first()?->getKey())
            ->filter()
            ->toArray();

        DB::beginTransaction();
        try {
            $token->update($request->all());
            $token->save();

            $token->scopes()->sync($scopes);

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }


        return redirect(route('front.panel.server-tokens.index'));
    }

    /**
     * Delete the specified resource in storage.
     */
    public function destroy(Request $request, int $tokenId): RedirectResponse
    {
        $token = ServerToken::find($tokenId);
        $token->scopes()->detach();
        $token->delete();

        return redirect(route('front.panel.server-tokens.index'));
    }
}
