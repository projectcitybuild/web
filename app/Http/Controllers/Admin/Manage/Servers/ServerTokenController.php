<?php

namespace App\Http\Controllers\Admin\Manage\Servers;

use App\Domains\Permissions\AuthorizesPermissions;
use App\Domains\Permissions\WebManagePermission;
use App\Http\Controllers\Admin\RendersAdminApp;
use App\Http\Controllers\WebController;
use App\Http\Data\AdminServerTokenData;
use App\Models\Server;
use App\Models\ServerToken;
use Illuminate\Http\Request;

class ServerTokenController extends WebController
{
    use AuthorizesPermissions;
    use RendersAdminApp;

    public function index(Request $request)
    {
        $this->requires(WebManagePermission::SERVER_TOKENS_VIEW);

        $tokens = ServerToken::orderBy('created_at', 'desc')
            ->get()
            ->map(fn (ServerToken $serverToken): array => AdminServerTokenData::fromModel($serverToken)->toArray());

        return $this->inertiaRender('ServerTokens/ServerTokensList', compact('tokens'));
    }

    public function create(Request $request)
    {
        $this->requires(WebManagePermission::SERVER_TOKENS_EDIT);

        return $this->inertiaRender('ServerTokens/ServerTokenCreate');
    }

    public function store(Request $request)
    {
        $this->requires(WebManagePermission::SERVER_TOKENS_EDIT);

        $validated = $request->validate([
            'token' => ['required', 'string'],
            'description' => ['required', 'string'],
            'allowed_ips' => 'nullable',
        ]);

        // TODO: remove server_id column
        $server = Server::first();
        $validated['server_id'] = $server->id;

        $allowedIps = $validated['allowed_ips'] ?? '';
        if (! empty($allowedIps)) {
            $validated['allowed_ips'] = collect(explode(PHP_EOL, $allowedIps))
                ->filter(fn ($ip) => ! empty($ip))
                ->join(',');
        }

        ServerToken::create($validated);

        return to_route('manage.server-tokens.index')
            ->with(['success' => 'Server token created successfully.']);
    }

    public function edit(ServerToken $serverToken)
    {
        $this->requires(WebManagePermission::SERVER_TOKENS_EDIT);

        return $this->inertiaRender('ServerTokens/ServerTokenEdit', [
            'token' => AdminServerTokenData::fromModel($serverToken),
        ]);
    }

    public function update(Request $request, ServerToken $serverToken)
    {
        $this->requires(WebManagePermission::SERVER_TOKENS_EDIT);

        $validated = $request->validate([
            'token' => ['required', 'string'],
            'description' => ['required', 'string'],
            'allowed_ips' => 'nullable',
        ]);

        $allowedIps = $validated['allowed_ips'];
        if (! empty($allowedIps)) {
            $validated['allowed_ips'] = collect(explode(PHP_EOL, $allowedIps))
                ->filter(fn ($ip) => ! empty($ip))
                ->join(',');
        }

        $serverToken->update($validated);

        return to_route('manage.server-tokens.index')
            ->with(['success' => 'Server token updated successfully.']);
    }

    public function destroy(Request $request, ServerToken $serverToken)
    {
        $this->requires(WebManagePermission::SERVER_TOKENS_EDIT);

        $serverToken->delete();

        return to_route('manage.server-tokens.index')
            ->with(['success' => 'Server token deleted successfully.']);
    }
}
