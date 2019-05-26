<?php

namespace App\Library\Discourse\Api;

use App\Library\Discourse\Entities\DiscoursePackedNonce;

class DiscourseSSOApi extends DiscourseAPIRequest
{
    /**
     * The normal way to get a nonce from Discourse is to redirect the user to
     * a specific Discourse (/session/sso) URL. Discourse will then redirect the
     * user back to our login form with an `sso` and `sig` URL parameter.
     * 
     * However, the double redirect is terrible for UX so instead we'll make
     * a call to the URL on the user's behalf and extract the `sso` and `sig`
     * parameter from the redirect response - all server side, so that no 
     * redirection is needed at all.
     *
     * @return DiscoursePackedNonce
     */
    public function requestNewPackedNonce(?string $returnPath = null) : DiscoursePackedNonce
    {
        $urlParameters = [];
        if ($returnPath !== null) {
            $urlParameters['query']['return_path'] = $returnPath;
        }

        $response = $this->client->get('session/sso', array_merge($urlParameters, [
            'allow_redirects' => [
                'max'               => 3,
                'track_redirects'   => true,
            ],
        ]));

        $redirectUri = $response->getHeaderLine('X-Guzzle-Redirect-History');
        
        $queryString = parse_url($redirectUri, PHP_URL_QUERY);
        
        $parameters = [];
        parse_str($queryString, $parameters);

        $packedNonce = new DiscoursePackedNonce($parameters['sso'], $parameters['sig']);

        return $packedNonce;
    }
}
