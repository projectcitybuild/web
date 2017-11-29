<?php
namespace App\Modules\Gallery\Services;

use GuzzleHttp\Client;

class ImgurApiService {

    /**
     * @var GuzzleHttp\Client
     */
    private $client;
    
    public function __construct(Client $client) {
        $this->client = $client;
    }

    /**
     * Fetches all images from the given imgur album hash
     *
     * @param String $albumHash
     * @return object
     */
    public function getImagesFromAlbum(String $albumHash) {
        $response = $this->client->request('GET', 'https://api.imgur.com/3/album/' . $albumHash . '/images', [
            'headers' => [
                'Authorization' => 'Client-ID ' . env('IMGUR_CLIENT_ID'),
            ],
        ]);

        if($response->getStatusCode() == 200) {
            $body = json_decode($response->getBody());
            return $body->data;
        }

        return null;
    }

}