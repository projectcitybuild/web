<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\Http\Requests\ServerStoreRequest;
use App\Http\Requests\ServerUpdateRequest;
use App\Models\Eloquent\Server;
use Illuminate\Http\JsonResponse;

class ManageServerController extends Controller
{
    public function index(): JsonResponse
    {
        $servers = Server::cursorPaginate(config('api.page_size'));

        return response()->json($servers);
    }

    public function store(ServerStoreRequest $request): JsonResponse
    {
        $server = Server::create($request->validated());

        return response()->json($server);
    }

    public function show(string $id): JsonResponse
    {
        $server = Server::findOrFail($id);

        return response()->json($server);
    }

    public function update(ServerUpdateRequest $request, string $id): JsonResponse
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
