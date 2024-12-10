<?php

namespace App\Http\Controllers\Manage;

use App\Core\Utilities\SecureTokenGenerator;
use App\Http\Controllers\WebController;
use App\Models\ServerToken;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServerTokenController extends WebController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Application|Factory|View
    {
        $tokens = ServerToken::orderby('created_at', 'desc')->paginate(100);

        return view('manage.pages.server-tokens.index')->with(compact('tokens'));
    }

    /**
     * Show the form for creating the specified resource.
     */
    public function create(Request $request, SecureTokenGenerator $tokenGenerator): Application|Factory|View
    {
        $token = new ServerToken();
        $generatedToken = $tokenGenerator->make();

        return view('manage.pages.server-tokens.create')
            ->with(compact('token', 'generatedToken'));
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
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        ServerToken::create([
            'token' => $request->get('token'),
            'server_id' => $request->get('server_id'),
            'description' => $request->get('description'),
        ]);

        return redirect(route('manage.server-tokens.index'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $tokenId): Application|Factory|View
    {
        $token = ServerToken::find($tokenId);

        return view('manage.pages.server-tokens.edit')->with(compact('token'));
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
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $token->update($request->all());
        $token->save();

        return redirect(route('manage.server-tokens.index'));
    }

    /**
     * Delete the specified resource in storage.
     */
    public function destroy(Request $request, int $tokenId): RedirectResponse
    {
        $token = ServerToken::find($tokenId);
        $token->delete();

        return redirect(route('manage.server-tokens.index'));
    }
}
