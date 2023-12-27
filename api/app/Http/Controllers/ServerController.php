<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServerRequest;
use App\Models\Eloquent\Server;
use Illuminate\Http\JsonResponse;

class ServerController extends Controller
{
    public function index(): JsonResponse
    {
        $servers = Server::get();

        return response()->json($servers);
    }

    public function store(ServerRequest $request): JsonResponse
    {
        $server = Server::create($request->validated());

        return response()->json($server);
    }

    public function show(string $id): JsonResponse
    {
        $server = Server::findOrFail($id);

        return response()->json($server);
    }

    public function update(ServerRequest $request, string $id): JsonResponse
    {
        $server = Server::findOrFail($id);
        $server->update($request->validated());
        $server->save();

        return response()->json($server);
    }

    public function destroy(string $id): JsonResponse
    {
        $server = Server::findOrFail($id);
        $server->delete();

        return response()->json();
    }
}
