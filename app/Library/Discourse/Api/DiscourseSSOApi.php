<?php

namespace App\Library\Discourse\Api;

use App\Library\Discourse\Entities\DiscoursePackedNonce;
use GuzzleHttp\Client;

class DiscourseSSOApi
{
    private Client $client;

    public function __construct(private DiscourseClientFactory $clientFactory)
    {
        $this->client = $this->clientFactory->make();
    }

    /**
     * The normal way to get a nonce from Discourse is to redirect the user to
     * a specific Discourse (/session/sso) URL. Discourse will then redirect the
     * user back to our login form with an `sso` and `sig` URL parameter.
     *
     * However, the double redirect is terrible for UX so instead we'll make
     * a call to the URL on the user's behalf and extract the `sso` and `sig`
     * parameter from the redirect response - all server side, so that no
     * redirection is needed at all.
     */
    public function requestNewPackedNonce(string $returnPath = '/latest'): DiscoursePackedNonce
    {
        $response = $this->client->get('session/sso?return_path='.$returnPath, [
            'allow_redirects' => [
                'max' => 3,
                'track_redirects' => true,
            ],
        ]);

        $redirectUri = $response->getHeaderLine('X-Guzzle-Redirect-History');

        $queryString = parse_url($redirectUri, PHP_URL_QUERY);

        $parameters = [];
        parse_str($queryString, $parameters);

        return new DiscoursePackedNonce($parameters['sso'], $parameters['sig']);
    }
}
