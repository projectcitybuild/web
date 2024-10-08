<?php

use App\Http\Middleware\RequireServerToken;
use App\Models\ServerToken;
use Illuminate\Support\Facades\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

describe('Unauthorized exception', function () {
    it('throws if token not present in header', function () {
        $request = Request::create(uri: 'foobar');
        expect($request->headers->has('Authorization'))->toBeFalse();

        $middleware = new RequireServerToken();
        $middleware->handle(
            request: $request,
            next: function () {},
        );
    })->expectExceptionObject(new HttpException(statusCode: 401));

    it('throws if token malformed', function (string $invalidToken) {
        $request = Request::create(uri: 'foobar');
        $request->headers->set(key: 'Authorization', values: $invalidToken);
        expect($request->headers->get('Authorization'))->toEqual($invalidToken);

        $middleware = new RequireServerToken();
        $middleware->handle(
            request: $request,
            next: function () {},
        );
    })->with(['invalid', 'Bearer', ''])
        ->expectExceptionObject(new HttpException(statusCode: 401));;

    it('throws if no matching ServerToken', function () {
        $request = Request::create(uri: 'foobar');
        $request->headers->set(key: 'Authorization', values: 'foobar');
        expect($request->headers->get('Authorization'))->toEqual('foobar');

        $this->assertDatabaseMissing('server_tokens', [
            'token' => 'foobar',
        ]);

        $middleware = new RequireServerToken();
        $middleware->handle(
            request: $request,
            next: function () {},
        );
    })->expectExceptionObject(new HttpException(statusCode: 401));
});

it('calls next middleware if matching ServerToken found', function () {
    $token = ServerToken::factory()->create();

    $request = Request::create(uri: 'test');
    $request->headers->set(key: 'Authorization', values: 'Bearer '.$token->token);
    expect($request->headers->get('Authorization'))->toEqual('Bearer '.$token->token);

    $didPass = false;
    $middleware = new RequireServerToken();
    $middleware->handle(
        request: $request,
        next: function () use (&$didPass) {
            $didPass = true;
        },
    );
    expect($didPass)->toBeTrue();
});
