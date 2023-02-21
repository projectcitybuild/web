<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServerRequest;
use App\Models\Eloquent\Server;
use Illuminate\Http\JsonResponse;

class ServerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $servers = Server::get();

        return response()->json($servers);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ServerRequest $request): JsonResponse
    {
        $server = Server::create($request->all());

        return response()->json($server);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $server = Server::findOrFail($id);

        return response()->json($server);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ServerRequest $request, string $id): JsonResponse
    {
        $server = Server::findOrFail($id);
        $server->update($request->all());
        $server->save();

        return response()->json($server);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $server = Server::findOrFail($id);
        $server->delete();

        return response()->json();
    }
}
