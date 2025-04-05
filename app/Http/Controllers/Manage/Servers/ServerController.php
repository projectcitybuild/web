<?php

namespace App\Http\Controllers\Manage\Servers;

use App\Http\Controllers\Manage\RendersManageApp;
use App\Http\Controllers\WebController;
use App\Models\Server;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ServerController extends WebController
{
    use RendersManageApp;

    public function index(Request $request)
    {
        Gate::authorize('viewAny', Server::class);

        $servers = Server::orderBy('created_at', 'desc')->get();

        return $this->inertiaRender('Servers/ServerList', compact('servers'));
    }

    public function create(Request $request)
    {
        Gate::authorize('create', Server::class);

        return $this->inertiaRender('Servers/ServerCreate');
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', Server::class);

        $validated = $request->validate([
            'name' => ['required'],
            'ip' => ['required'],
            'port' => ['required', 'int'],
            'web_port' => ['nullable', 'int'],
        ]);

        Server::create($validated);

        return to_route('manage.servers.index')
            ->with(['success' => 'Server created successfully.']);
    }

    public function edit(Server $server)
    {
        Gate::authorize('update', $server);

        return $this->inertiaRender('Servers/ServerEdit', compact('server'));
    }

    public function update(Request $request, Server $server)
    {
        Gate::authorize('update', $server);

        $validated = $request->validate([
            'name' => ['required'],
            'ip' => ['required'],
            'port' => ['required', 'int'],
            'web_port' => ['nullable', 'int'],
        ]);

        $server->update($validated);

        return to_route('manage.servers.index')
            ->with(['success' => 'Server updated successfully.']);
    }

    public function destroy(Request $request, Server $server)
    {
        Gate::authorize('delete', $server);

        $server->delete();

        return to_route('manage.servers.index')
            ->with(['success' => 'Server deleted successfully.']);
    }
}
