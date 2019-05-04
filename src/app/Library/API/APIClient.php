<?php

namespace App\Library\API;

use GuzzleHttp\Client;
use App\Exceptions\NotImplementedException;
use Psr\Http\Message\ResponseInterface;

final class APIClient
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function call(APIRequest $request)
    {
        $response = $this->performHttpRequest($request);
        
        return json_decode($response->getBody(), true);
    }

    private function performHttpRequest(APIRequest $request) : ResponseInterface
    {
        $path    = $request->getPath();
        $options = $request->getRequestURLParams();

        switch ($request->getHttpMethod()) {
            case HttpMethodType::GET:
                return $this->client->get($path, $options);

            case HttpMethodType::POST:
                $formBody = $request->getRequestBody();
                if ($formBody !== null) {
                    $options = array_merge($options, [
                        'form_params' => $request->getRequestBody(),
                    ]);
                }
                return $this->client->post($path, $options);
            
            case HttpMethodType::PATCH:
                throw new NotImplementedException();

            case HttpMethodType::PUT:
                throw new NotImplementedException();

            case HttpMethodType::DELETE:
                throw new NotImplementedException();
        }
    }
}