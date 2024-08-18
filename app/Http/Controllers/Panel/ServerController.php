<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\WebController;
use App\Models\Server;
use App\Models\ServerCategory;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServerController extends WebController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Application|Factory|View
    {
        $servers = Server::orderby('created_at', 'desc')->paginate(100);

        return view('admin.servers.index')->with(compact('servers'));
    }

    /**
     * Show the form for creating the specified resource.
     */
    public function create(Request $request): Application|Factory|View
    {
        $server = new Server();

        return view('admin.servers.create')
            ->with(compact('server'));
    }

    /**
     * Add a specified resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'ip' => 'required|ip',
            'port' => 'required|int',
            'ip_alias' => 'string',
            'display_order' => 'required|int',
            'game_type' => 'required|int',
            'is_visible' => 'boolean',
            'is_port_visible' => 'boolean',
            'is_querying' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Server::create([
            'server_category_id' => ServerCategory::first()->getKey(),
            'name' => $request->get('name'),
            'ip' => $request->get('ip'),
            'port' => $request->get('port'),
            'ip_alias' => $request->get('ip_alias'),
            'display_order' => $request->get('display_order'),
            'game_type' => $request->get('game_type'),
            'is_visible' => $request->get('is_visible', false),
            'is_port_visible' => $request->get('is_port_visible', false),
            'is_querying' => $request->get('is_querying', false),
        ]);

        return redirect(route('front.panel.servers.index'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $serverId): Application|Factory|View
    {
        $server = Server::find($serverId);

        return view('admin.servers.edit')->with(compact('server'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $serverId): RedirectResponse
    {
        $server = Server::find($serverId);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'ip' => 'required|ip',
            'port' => 'required|int',
            'ip_alias' => 'string',
            'display_order' => 'required|int',
            'game_type' => 'required|int',
            'is_visible' => 'boolean',
            'is_port_visible' => 'boolean',
            'is_querying' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // TODO: this can be done in a single update
        $server->update($request->all());
        $server->is_visible = $request->get('is_visible', false);
        $server->is_port_visible = $request->get('is_port_visible', false);
        $server->is_querying = $request->get('is_querying', false);
        $server->save();

        return redirect(route('front.panel.servers.index'));
    }

    /**
     * Delete the specified resource in storage.
     */
    public function destroy(Request $request, int $serverId): RedirectResponse
    {
        $server = Server::find($serverId);
        $server->delete();

        return redirect(route('front.panel.servers.index'));
    }
}
