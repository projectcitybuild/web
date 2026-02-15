<?php

namespace App\Http\Controllers\Manage\Servers;

use App\Domains\Permissions\AuthorizesPermissions;
use App\Domains\Permissions\WebManagePermission;
use App\Http\Controllers\Manage\RendersManageApp;
use App\Http\Controllers\WebController;
use App\Models\Server;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ServerController extends WebController
{
    use AuthorizesPermissions;
    use RendersManageApp;

    public function index(Request $request)
    {
        $this->requires(WebManagePermission::SERVERS_VIEW);

        $servers = Server::orderBy('created_at', 'desc')->get();

        return $this->inertiaRender('Servers/ServerList', compact('servers'));
    }

    public function create(Request $request)
    {
        $this->requires(WebManagePermission::SERVERS_EDIT);

        return $this->inertiaRender('Servers/ServerCreate');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->requires(WebManagePermission::SERVERS_EDIT);

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
        $this->requires(WebManagePermission::SERVERS_EDIT);

        return $this->inertiaRender('Servers/ServerEdit', compact('server'));
    }

    public function update(Request $request, Server $server)
    {
        $this->requires(WebManagePermission::SERVERS_EDIT);

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
        $this->requires(WebManagePermission::SERVERS_EDIT);

        $server->delete();

        return to_route('manage.servers.index')
            ->with(['success' => 'Server deleted successfully.']);
    }
}
