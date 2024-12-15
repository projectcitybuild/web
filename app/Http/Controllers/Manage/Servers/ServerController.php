<?php

namespace App\Http\Controllers\Manage\Servers;

use App\Http\Controllers\WebController;
use App\Models\Server;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ServerController extends WebController
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Server::class);

        $servers = Server::orderby('created_at', 'desc')
            ->paginate(100);

        return view('manage.pages.servers.index')
            ->with(compact('servers'));
    }

    public function create(Request $request)
    {
        Gate::authorize('create', Server::class);

        $server = new Server();

        return view('manage.pages.servers.create')
            ->with(compact('server'));
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', Server::class);

        $validated = $request->validate([
            'name' => 'required|string',
            'ip' => ['required'],
            'ip_alias' => ['nullable', 'string'],
            'port' => ['required', 'int'],
            'web_port' => ['nullable', 'int'],
        ]);

        Server::create($validated);

        return redirect(route('manage.servers.index'));
    }

    public function edit(Server $server)
    {
        Gate::authorize('update', $server);

        return view('manage.pages.servers.edit')
            ->with(compact('server'));
    }

    public function update(Request $request, Server $server)
    {
        Gate::authorize('update', $server);

        $validated = $request->validate([
            'name' => 'required|string',
            'ip' => ['required'],
            'ip_alias' => ['nullable', 'string'],
            'port' => ['required', 'int'],
            'web_port' => ['nullable', 'int'],
        ]);

        $server->update($validated);

        return redirect(route('manage.servers.index'));
    }

    public function destroy(Request $request, Server $server)
    {
        Gate::authorize('delete', $server);

        $server->delete();

        return redirect(route('manage.servers.index'));
    }
}
