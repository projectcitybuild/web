<?php

namespace App\Http\Controllers;

use App\Http\WebController;
use App\Library\Discourse\Authentication\DiscourseLoginHandler;
use App\Library\Discourse\Exceptions\BadSSOPayloadException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

final class DiscourseSSOController extends WebController
{
    private const DISCOURSE_SSO_ENDPOINT = 'session/sso';

    /**
     * @var DiscourseLoginHandler
     */
    private $discourseLoginHandler;

    /**
     * DiscourseSSOController constructor.
     */
    public function __construct(DiscourseLoginHandler $discourseLoginHandler)
    {
        $this->discourseLoginHandler = $discourseLoginHandler;
    }

    public function create(Request $request)
    {
        $account = $request->user();

        if (! $request->has('sso') || ! $request->has('sig')) {
            return redirect(config('discourse.base_url').self::DISCOURSE_SSO_ENDPOINT);
        }

        try {
            $endpoint = $this->discourseLoginHandler->getRedirectUrl(
                $request,
                $account->getKey(),
                $account->email,
                $account->username,
                $account->discourseGroupString()
            );
        } catch (BadSSOPayloadException $e) {
            Log::debug('Missing nonce or return key in session', ['session' => $request->session()]);
            throw $e;
        }

        return redirect()->to($endpoint);
    }
}
