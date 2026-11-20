<?php

namespace App\Http\Controllers\Admin\Manage\PassportClients;

use App\Domains\Permissions\AuthorizesPermissions;
use App\Domains\Permissions\WebManagePermission;
use App\Http\Controllers\Admin\RendersAdminApp;
use App\Http\Controllers\WebController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Response;
use Laravel\Passport\Client;
use Laravel\Passport\ClientRepository;

final class PassportClientController extends WebController
{
    use AuthorizesPermissions;
    use RendersAdminApp;

    public function index(Request $request): Response
    {
        $this->requires(WebManagePermission::SERVER_TOKENS_VIEW);

        return $this->inertiaRender('PassportClients/PassportClientList', [
            'clients' => Client::query()
                ->orderByDesc('id')
                ->get()
                ->map(fn (Client $client): array => $this->serializeClient($client))
                ->all(),
            'created_client_secret' => $this->can(WebManagePermission::SERVER_TOKENS_EDIT)
                ? $request->session()->get('created_client_secret')
                : null,
        ]);
    }

    public function create(): Response
    {
        $this->requires(WebManagePermission::SERVER_TOKENS_EDIT);

        return $this->inertiaRender('PassportClients/PassportClientCreate');
    }

    public function store(Request $request, ClientRepository $clients): RedirectResponse
    {
        $this->requires(WebManagePermission::SERVER_TOKENS_EDIT);

        $this->normalizeRedirectUris($request);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:191'],
            'grant_type' => ['required', Rule::in(['authorization_code', 'client_credentials'])],
            'confidential' => ['exclude_unless:grant_type,authorization_code', 'required', 'boolean'],
            'redirect_uris' => ['exclude_unless:grant_type,authorization_code', 'required', 'array', 'min:1'],
            'redirect_uris.*' => ['required', 'string', 'url', 'max:2048'],
        ]);

        $client = match ($validated['grant_type']) {
            'authorization_code' => $clients->createAuthorizationCodeGrantClient(
                $validated['name'],
                $validated['redirect_uris'],
                (bool) $validated['confidential'],
            ),
            'client_credentials' => $clients->createClientCredentialsGrantClient($validated['name']),
        };

        return to_route('manage.passport-clients.index')
            ->with([
                'success' => $client->plainSecret === null
                    ? 'Passport client created successfully.'
                    : 'Passport client created. Copy its secret now; it cannot be retrieved again.',
                'created_client_secret' => $client->plainSecret,
            ]);
    }

    public function edit(Request $request, Client $passportClient): Response
    {
        $this->requires(WebManagePermission::SERVER_TOKENS_EDIT);
        abort_if($passportClient->revoked, 404);

        $regeneratedClientSecret = $request->session()->get('regenerated_client_secret');
        $regeneratedClientId = $request->session()->get('regenerated_client_id');

        return $this->inertiaRender('PassportClients/PassportClientEdit', [
            'client' => $this->serializeClient($passportClient),
            'regenerated_client_secret' => is_string($regeneratedClientSecret)
                && (string) $regeneratedClientId === (string) $passportClient->getKey()
                    ? $regeneratedClientSecret
                    : null,
        ]);
    }

    public function update(Request $request, Client $passportClient): RedirectResponse
    {
        $this->requires(WebManagePermission::SERVER_TOKENS_EDIT);
        abort_if($passportClient->revoked, 404);

        $rules = [
            'name' => ['required', 'string', 'max:191'],
        ];

        $hasRedirectUris = $passportClient->hasGrantType('authorization_code')
            || $passportClient->redirect_uris !== [];

        if ($hasRedirectUris) {
            $this->normalizeRedirectUris($request);

            $rules['redirect_uris'] = ['required', 'array', 'min:1'];
            $rules['redirect_uris.*'] = ['required', 'string', 'url', 'max:2048'];
        }

        $validated = $request->validate($rules);
        $columns = $passportClient->getConnection()->getSchemaBuilder()->getColumnListing($passportClient->getTable());

        $passportClient->forceFill([
            'name' => $validated['name'],
            ...(
                $hasRedirectUris
                    ? (in_array('redirect_uris', $columns, true)
                        ? ['redirect_uris' => $validated['redirect_uris']]
                        : ['redirect' => implode(',', $validated['redirect_uris'])])
                    : []
            ),
        ])->save();

        return to_route('manage.passport-clients.index')
            ->with(['success' => 'Passport client updated successfully.']);
    }

    public function regenerateSecret(Client $passportClient, ClientRepository $clients): RedirectResponse
    {
        $this->requires(WebManagePermission::SERVER_TOKENS_EDIT);
        abort_if($passportClient->revoked || ! $passportClient->confidential(), 404);
        abort_unless($clients->regenerateSecret($passportClient), 500, 'Passport client secret could not be regenerated.');

        $newSecret = $passportClient->plainSecret;
        abort_unless(is_string($newSecret), 500, 'Passport client secret could not be retrieved after regeneration.');

        return to_route('manage.passport-clients.edit', $passportClient)
            ->with([
                'success' => 'Passport client secret regenerated. The previous secret is no longer valid.',
                'regenerated_client_secret' => $newSecret,
                'regenerated_client_id' => $passportClient->getKey(),
            ]);
    }

    public function destroy(Client $passportClient, ClientRepository $clients): RedirectResponse
    {
        $this->requires(WebManagePermission::SERVER_TOKENS_EDIT);
        abort_if($passportClient->revoked, 404);

        $clients->delete($passportClient);

        return to_route('manage.passport-clients.index')
            ->with(['success' => 'Passport client and its active tokens were revoked.']);
    }

    /**
     * @return array{id: int, name: string, grant_types: list<string>, redirect_uris: list<string>, confidential: bool, revoked: bool}
     */
    private function serializeClient(Client $client): array
    {
        return [
            'id' => (int) $client->getKey(),
            'name' => $client->name,
            'grant_types' => array_values($client->grant_types),
            'redirect_uris' => array_values($client->redirect_uris),
            'confidential' => $client->confidential(),
            'revoked' => $client->revoked,
        ];
    }

    private function normalizeRedirectUris(Request $request): void
    {
        $redirectUris = $request->input('redirect_uris');

        if (! is_string($redirectUris)) {
            return;
        }

        $request->merge([
            'redirect_uris' => array_values(array_filter(
                array_map('trim', preg_split('/\R/', $redirectUris) ?: []),
                static fn (string $redirectUri): bool => $redirectUri !== '',
            )),
        ]);
    }
}
