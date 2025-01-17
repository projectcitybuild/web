<?php

namespace App\Http\Controllers\Manage\Servers;

use App\Domains\Manage\RendersManageApp;
use App\Http\Controllers\WebController;
use App\Models\Server;
use App\Models\ServerToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ServerTokenController extends WebController
{
    use RendersManageApp;

    public function index(Request $request)
    {
        Gate::authorize('viewAny', ServerToken::class);

        $tokens = ServerToken::orderBy('created_at', 'desc')->get();

        return $this->inertiaRender('ServerTokens/ServerTokensList', compact('tokens'));
    }

    public function create(Request $request)
    {
        Gate::authorize('create', ServerToken::class);

        return $this->inertiaRender('ServerTokens/ServerTokenCreate');
    }

    public function store(Request $request)
    {
        Gate::authorize('create', ServerToken::class);

        $validated = $request->validate([
            'token' => ['required', 'string'],
            'description' => ['required', 'string'],
        ]);

        // TODO: remove server_id column
        $server = Server::first();
        $validated['server_id'] = $server->getKey();

        ServerToken::create($validated);

        return to_route('manage.server-tokens.index')
            ->with(['success' => 'Server token created successfully.']);
    }

    public function edit(ServerToken $serverToken)
    {
        Gate::authorize('update', ServerToken::class);

        return $this->inertiaRender('ServerTokens/ServerTokenEdit', [
            'token' => $serverToken,
        ]);
    }

    public function update(Request $request, ServerToken $serverToken)
    {
        Gate::authorize('update', $serverToken);

        $validated = $request->validate([
            'token' => ['required', 'string'],
            'description' => ['required', 'string'],
        ]);

        $serverToken->update($validated);

        return to_route('manage.server-tokens.index')
            ->with(['success' => 'Server token updated successfully.']);
    }

    public function destroy(Request $request, ServerToken $serverToken)
    {
        Gate::authorize('delete', $serverToken);

        $serverToken->delete();

        return to_route('manage.server-tokens.index')
            ->with(['success' => 'Server token deleted successfully.']);
    }
}
