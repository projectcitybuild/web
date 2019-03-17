<?php

namespace Domains\Library\Discourse\Api;

final class DiscourseSSOApi extends DiscourseAPIRequest
{
    public function requestNonce(string $returnPath = '/latest') : array
    {
        $response = $this->client->get('session/sso?return_path='.$returnPath, [
            'allow_redirects' => [
                'max'               => 3,
                'track_redirects'   => true,
            ],
        ]);

        $redirectUri = $response->getHeaderLine('X-Guzzle-Redirect-History');
        
        $queryString = parse_url($redirectUri, PHP_URL_QUERY);
        
        $parameters = [];
        parse_str($queryString, $parameters);

        return $parameters;
    }
}
