<?php

namespace Shared\ExternalAccounts\Session\Adapters;

use App\Entities\Models\Eloquent\Account;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Library\Discourse\Api\DiscourseAdminApi;
use Library\Discourse\Api\DiscourseUserApi;
use Library\Discourse\Authentication\DiscourseLoginHandler;
use Library\Discourse\Entities\DiscoursePackedNonce;
use Library\Discourse\Exceptions\BadSSOPayloadException;
use Shared\ExternalAccounts\Session\ExternalAccountsSession;

/**
 * @final
 */
class DiscourseAccountSession implements ExternalAccountsSession
{
    private const DISCOURSE_SSO_ENDPOINT = 'session/sso';

    public function __construct(
        private DiscourseUserApi $discourseUserApi,
        private DiscourseAdminApi $discourseAdminApi,
        private DiscourseLoginHandler $discourseLoginHandler,
    ) {}

    public function login(Account $account, ?DiscoursePackedNonce $nonce = null): RedirectResponse
    {
        // If the user clicks 'Login' on the PCB side (as opposed to the Discourse side),
        // we need to redirect to Discourse, which will then redirect us back here with a nonce
        if ($nonce === null) {
            $path = config('discourse.base_url') . self::DISCOURSE_SSO_ENDPOINT;
            return redirect(to: $path);
        }

        try {
            $endpoint = $this->discourseLoginHandler->getRedirectUrl(
                $nonce,
                $account->getKey(),
                $account->email,
                $account->username,
                $account->discourseGroupString()
            );
        } catch (BadSSOPayloadException $e) {
            Log::debug('Missing nonce or return key in session');
            throw $e;
        }

        return redirect()->to($endpoint);
    }

    public function logout(int $pcbAccountId): void
    {
        $user = $this->getDiscourseUser($pcbAccountId);

        Log::info('Logging out user', ['account_id' => $pcbAccountId]);

        try {
            $this->discourseAdminApi->requestLogout($user['id']);
        } catch (ClientException $error) {
            // Discourse will throw a 404 error if the user attempts to logout but isn't
            // currently logged-in. If that happens, we'll just fail silently
            if ($error->getCode() !== 404) {
                Log::notice('Caught a 404 error logging out of Discourse');
                throw $error;
            }
        }
    }

    /**
     * Fetches the Discourse user associated with the given PCB account ID.
     *
     * @throws \Exception if Discourse API response does not contain a `user` key
     */
    private function getDiscourseUser(int $pcbId): array
    {
        $result = $this->discourseUserApi->fetchUserByPcbId($pcbId);

        $user = $result['user']
            ?? throw new \Exception('Discourse logout api response did not have a `user` key');

        Log::debug('Fetched Discord user', [
            'id' => $user['id'],
            'username' => $user['username'],
            'response' => $result,
        ]);

        return $user;
    }
}
