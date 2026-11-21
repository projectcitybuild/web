<?php

namespace App\Http\Controllers\Front\Account\Settings;

use App\Core\Domains\Auditing\AuditEventRecorder;
use App\Core\Domains\Discord\DiscordRoleSyncDispatcher;
use App\Core\Domains\Discord\DiscordSnowflake;
use App\Domains\Discord\UseCases\DisconnectDiscordAccount;
use App\Http\Controllers\WebController;
use App\Models\Account;
use App\Models\ExternalAccount;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;

final class DiscordAccountController extends WebController
{
    public function show(Request $request): View
    {
        $account = $this->account($request);
        $externalAccount = $account->externalAccounts()
            ->where('provider', ExternalAccount::PROVIDER_DISCORD)
            ->first();

        return view('front.pages.account.settings.discord', compact('externalAccount'));
    }

    public function connect(Request $request): RedirectResponse
    {
        $clientId = config('services.discord.client_id');
        $redirectUri = config('services.discord.redirect_uri');

        abort_unless(
            is_string($clientId) && $clientId !== '' && is_string($redirectUri) && $redirectUri !== '',
            503,
            'Discord account linking is not configured.',
        );

        $state = Str::random(40);
        $request->session()->put('discord.oauth_state', $state);

        $query = http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => 'identify',
            'state' => $state,
        ], '', '&', PHP_QUERY_RFC3986);

        return redirect()->away('https://discord.com/oauth2/authorize?'.$query);
    }

    public function callback(
        Request $request,
        DiscordRoleSyncDispatcher $dispatcher,
        AuditEventRecorder $auditEventRecorder,
    ): RedirectResponse {
        $expectedState = $request->session()->pull('discord.oauth_state');
        $receivedState = $request->string('state')->toString();

        if (! is_string($expectedState) || ! hash_equals($expectedState, $receivedState)) {
            return to_route('front.account.settings.discord')
                ->with('error', 'Discord account verification could not be completed. Please try again.');
        }

        if ($request->query('error') !== null) {
            return to_route('front.account.settings.discord')
                ->with('error', 'Discord account linking was cancelled.');
        }

        $clientId = config('services.discord.client_id');
        $clientSecret = config('services.discord.client_secret');
        $redirectUri = config('services.discord.redirect_uri');
        $code = $request->string('code')->toString();

        abort_unless(
            is_string($clientId)
                && $clientId !== ''
                && is_string($clientSecret)
                && $clientSecret !== ''
                && is_string($redirectUri)
                && $redirectUri !== '',
            503,
            'Discord account linking is not configured.',
        );

        if ($code === '') {
            return to_route('front.account.settings.discord')
                ->with('error', 'Discord did not provide an authorization code. Please try again.');
        }

        try {
            $tokenResponse = Http::asForm()
                ->connectTimeout(3)
                ->timeout(10)
                ->post('https://discord.com/api/oauth2/token', [
                    'client_id' => $clientId,
                    'client_secret' => $clientSecret,
                    'grant_type' => 'authorization_code',
                    'code' => $code,
                    'redirect_uri' => $redirectUri,
                ])
                ->throw();

            $accessToken = $tokenResponse->json('access_token');

            if (! is_string($accessToken) || $accessToken === '') {
                throw new RuntimeException('Discord OAuth response did not contain an access token.');
            }

            $identityResponse = Http::connectTimeout(3)
                ->timeout(10)
                ->withToken($accessToken)
                ->get('https://discord.com/api/users/@me')
                ->throw();

            $identity = $identityResponse->json();
        } catch (ConnectionException|RequestException|RuntimeException $exception) {
            Log::warning('Discord account linking could not verify the remote identity.', [
                'account_id' => $this->account($request)->getKey(),
                'exception_class' => $exception::class,
            ]);

            return to_route('front.account.settings.discord')
                ->with('error', 'Discord account verification failed. Please try again later.');
        }

        $discordUserId = is_array($identity) ? ($identity['id'] ?? null) : null;
        $username = is_array($identity) ? ($identity['username'] ?? null) : null;

        if (
            ! is_string($discordUserId)
            || ! DiscordSnowflake::isValid($discordUserId)
            || ! is_string($username)
            || $username === ''
            || mb_strlen($username) > 100
        ) {
            Log::warning('Discord account linking received an invalid identity response.', [
                'account_id' => $this->account($request)->getKey(),
            ]);

            return to_route('front.account.settings.discord')
                ->with('error', 'Discord account verification failed. Please try again later.');
        }

        $account = $this->account($request);
        $linkCreated = DB::transaction(function () use ($account, $discordUserId, $username, $auditEventRecorder): ?bool {
            Account::query()
                ->whereKey($account->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $identityLink = ExternalAccount::query()
                ->where('provider', ExternalAccount::PROVIDER_DISCORD)
                ->where('provider_user_id', $discordUserId)
                ->lockForUpdate()
                ->first();

            if ($identityLink !== null && (int) $identityLink->account_id !== (int) $account->getKey()) {
                return null;
            }

            $accountLink = $account->externalAccounts()
                ->where('provider', ExternalAccount::PROVIDER_DISCORD)
                ->lockForUpdate()
                ->first();

            if ($accountLink !== null && $accountLink->provider_user_id !== $discordUserId) {
                return null;
            }

            if ($accountLink === null) {
                ExternalAccount::create([
                    'account_id' => $account->getKey(),
                    'provider' => ExternalAccount::PROVIDER_DISCORD,
                    'provider_user_id' => $discordUserId,
                    'username' => $username,
                    'verified_at' => now(),
                ]);

                $auditEventRecorder->record(
                    subject: $account,
                    event: 'connected Discord account',
                    newValues: [
                        'discord_user_id' => $discordUserId,
                        'discord_username' => $username,
                    ],
                );
            } else {
                $oldUsername = $accountLink->username;
                $accountLink->update([
                    'username' => $username,
                    'verified_at' => now(),
                ]);

                $auditEventRecorder->record(
                    subject: $account,
                    event: 'reverified Discord account',
                    oldValues: ['discord_username' => $oldUsername],
                    newValues: ['discord_username' => $username],
                );
            }

            return $accountLink === null;
        });

        if ($linkCreated === null) {
            return to_route('front.account.settings.discord')
                ->with('error', 'This Discord account is already linked, or your account already has a different Discord account linked.');
        }

        $dispatcher->dispatchForDiscordUserId($discordUserId, 'linked');

        return to_route('front.account.settings.discord')
            ->with('success', 'Your Discord account has been linked.');
    }

    public function unlink(
        Request $request,
        DisconnectDiscordAccount $disconnectDiscordAccount,
    ): RedirectResponse {
        $account = $this->account($request);
        $discordUserId = $disconnectDiscordAccount->execute($account);

        return to_route('front.account.settings.discord')
            ->with('success', $discordUserId === null
                ? 'No Discord account was linked.'
                : 'Your Discord account has been unlinked.');
    }

    public function sync(Request $request, DiscordRoleSyncDispatcher $dispatcher): RedirectResponse
    {
        $account = $this->account($request);
        $discordUserId = $account->externalAccounts()
            ->where('provider', ExternalAccount::PROVIDER_DISCORD)
            ->value('provider_user_id');

        if (! is_string($discordUserId)) {
            return to_route('front.account.settings.discord')
                ->with('error', 'Link a Discord account before requesting a role sync.');
        }

        $dispatcher->dispatchForDiscordUserId($discordUserId);

        return to_route('front.account.settings.discord')
            ->with('success', 'A Discord role sync has been queued.');
    }

    private function account(Request $request): Account
    {
        $account = $request->user();
        abort_unless($account instanceof Account, 401);

        return $account;
    }
}
