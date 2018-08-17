<?php
namespace Domains\Library\Discourse\Api;

class DiscourseSSOApi
{
    /**
     * @var DiscourseClient
     */
    private $client;

    public function __construct(DiscourseClient $client)
    {
        $this->client = $client;
    }

    public function requestNonce() : array
    {
        $response = $this->client->get('session/sso?return_path=/latest', [
            'allow_redirects' => [
                'max'               => 10,
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
