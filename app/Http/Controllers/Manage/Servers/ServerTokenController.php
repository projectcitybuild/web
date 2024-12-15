<?php

namespace App\Http\Controllers\Manage\Servers;

use App\Core\Utilities\SecureTokenGenerator;
use App\Http\Controllers\WebController;
use App\Models\ServerToken;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class ServerTokenController extends WebController
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', ServerToken::class);

        $tokens = ServerToken::orderby('created_at', 'desc')
            ->paginate(100);

        return view('manage.pages.server-tokens.index')
            ->with(compact('tokens'));
    }

    public function create(Request $request, SecureTokenGenerator $tokenGenerator)
    {
        Gate::authorize('create', ServerToken::class);

        $token = new ServerToken();
        $generatedToken = $tokenGenerator->make();

        return view('manage.pages.server-tokens.create')
            ->with(compact('token', 'generatedToken'));
    }

    public function store(Request $request)
    {
        Gate::authorize('create', ServerToken::class);

        $validated = $request->validate([
            'token' => ['required', 'string'],
            'server_id' => ['nullable', 'int', 'exists:servers,server_id'],
            'description' => ['required', 'string'],
        ]);

        ServerToken::create($validated);

        return redirect(route('manage.server-tokens.index'));
    }

    public function edit(ServerToken $serverToken)
    {
        Gate::authorize('update', ServerToken::class);

        return view('manage.pages.server-tokens.edit')
            ->with(compact('serverToken'));
    }

    public function update(Request $request, ServerToken $serverToken)
    {
        Gate::authorize('update', $serverToken);

        $validated = $request->validate([
            'token' => ['required', 'string'],
            'server_id' => ['nullable', 'int', 'exists:servers,server_id'],
            'description' => ['required', 'string'],
        ]);

        $serverToken->update($validated);

        return redirect(route('manage.server-tokens.index'));
    }

    public function destroy(Request $request, ServerToken $serverToken)
    {
        Gate::authorize('delete', $serverToken);

        $serverToken->delete();

        return redirect(route('manage.server-tokens.index'));
    }
}
