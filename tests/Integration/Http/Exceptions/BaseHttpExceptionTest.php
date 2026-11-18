<?php

use App\Core\Data\Exceptions\NotImplementedException;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    $apiHost = parse_url(config('app.api_url'), PHP_URL_HOST);

    Route::domain($apiHost)->get('/v3/test-base-http-exception', fn () => throw new NotImplementedException);
    Route::get('/test-base-http-exception', fn () => throw new NotImplementedException);
});

it('renders the custom error envelope on the configured API host and path', function () {
    $this->getJson(config('app.api_url').'/v3/test-base-http-exception')
        ->assertStatus(503)
        ->assertExactJson([
            'error' => [
                'id' => 'not_implemented',
                'title' => '',
                'detail' => 'This feature is not implemented. Please report this error.',
                'status' => 503,
            ],
        ]);
});

it('does not render the custom API error envelope for web requests', function () {
    $this->getJson(config('app.url').'/test-base-http-exception')
        ->assertJsonMissingPath('error.id');
});
