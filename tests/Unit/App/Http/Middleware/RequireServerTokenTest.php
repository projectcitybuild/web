<?php

use App\Http\Middleware\RequireServerToken;
use App\Models\ServerToken;
use Illuminate\Support\Facades\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

describe('Token parsing', function () {
    it('throws if token not present in header', function () {
        $request = Request::create(uri: 'foobar');
        expect($request->headers->has('Authorization'))->toBeFalse();

        $middleware = new RequireServerToken;
        $middleware->handle(
            request: $request,
            next: function () {},
        );
    })->expectExceptionObject(
        new HttpException(statusCode: 401),
    );

    it('throws if token malformed', function (string $invalidToken) {
        $request = Request::create(uri: 'foobar');
        $request->headers->set(key: 'Authorization', values: $invalidToken);
        expect($request->headers->get('Authorization'))->toEqual($invalidToken);

        $middleware = new RequireServerToken;
        $middleware->handle(
            request: $request,
            next: function () {},
        );
    })->with(['invalid', 'Bearer', ''])
        ->expectExceptionObject(
            new HttpException(statusCode: 401, message: 'Invalid authorization header. Must be a bearer token'),
        );

    it('throws if no matching ServerToken', function () {
        $request = Request::create(uri: 'foobar');
        $request->headers->set(key: 'Authorization', values: 'Bearer foobar');
        expect($request->headers->get('Authorization'))->toEqual('Bearer foobar');

        $this->assertDatabaseMissing('server_tokens', [
            'token' => 'foobar',
        ]);

        $middleware = new RequireServerToken;
        $middleware->handle(
            request: $request,
            next: function () {},
        );
    })->expectExceptionObject(
        new HttpException(statusCode: 401),
    );
});

describe('IP whitelist', function () {
    it('throws 401 if client ip undetermined', function () {
        $token = ServerToken::factory()->create();
        $request = Request::create(uri: 'foobar');
        $request->headers->set(key: 'Authorization', values: 'Bearer '.$token->token);
        $request->server->add(['REMOTE_ADDR' => '']);

        $middleware = new RequireServerToken;
        $middleware->handle(
            request: $request,
            next: function () {},
        );
    })->expectExceptionObject(
        new HttpException(statusCode: 401, message: 'Could not determine IP address'),
    );

    it('throws 403 if client ip does not match', function () {
        $allowedIp = '192.168.0.1';

        expect(function () use ($allowedIp) {
            $token = ServerToken::factory()->create(['allowed_ips' => $allowedIp]);
            $request = Request::create(uri: 'foobar');
            $request->headers->set(key: 'Authorization', values: 'Bearer '.$token->token);
            $request->server->add(['REMOTE_ADDR' => '192.168.0.2']);

            $middleware = new RequireServerToken;
            $middleware->handle(
                request: $request,
                next: function () {},
            );
        })->toThrow(
            new HttpException(statusCode: 401, message: 'IP address is not whitelisted'),
        );

        expect(function () use ($allowedIp) {
            $token = ServerToken::factory()->create(['allowed_ips' => $allowedIp]);
            $request = Request::create(uri: 'foobar');
            $request->headers->set(key: 'Authorization', values: 'Bearer '.$token->token);
            $request->server->add(['REMOTE_ADDR' => '192.168.0.1']);

            $middleware = new RequireServerToken;
            $middleware->handle(
                request: $request,
                next: function () {},
            );
        })->not()->toThrow(HttpException::class);
    });

    it('throws 403 if client ip not in list of allowed ips', function () {
        $allowedIps = '192.168.0.1,192.168.1.1';

        expect(function () use ($allowedIps) {
            $token = ServerToken::factory()->create(['allowed_ips' => $allowedIps]);
            $request = Request::create(uri: 'foobar');
            $request->headers->set(key: 'Authorization', values: 'Bearer '.$token->token);
            $request->server->add(['REMOTE_ADDR' => '192.168.0.2']);

            $middleware = new RequireServerToken;
            $middleware->handle(
                request: $request,
                next: function () {},
            );
        })->toThrow(
            new HttpException(statusCode: 401, message: 'IP address is not whitelisted'),
        );

        expect(function () use ($allowedIps) {
            $token = ServerToken::factory()->create(['allowed_ips' => $allowedIps]);
            $request = Request::create(uri: 'foobar');
            $request->headers->set(key: 'Authorization', values: 'Bearer '.$token->token);
            $request->server->add(['REMOTE_ADDR' => '192.168.1.1']);

            $middleware = new RequireServerToken;
            $middleware->handle(
                request: $request,
                next: function () {},
            );
        })->not()->toThrow(HttpException::class);
    });
});

it('calls next middleware if matching ServerToken found', function () {
    $token = ServerToken::factory()->create();

    $request = Request::create(uri: 'test');
    $request->headers->set(key: 'Authorization', values: 'Bearer '.$token->token);
    expect($request->headers->get('Authorization'))->toEqual('Bearer '.$token->token);

    $didPass = false;
    $middleware = new RequireServerToken;
    $middleware->handle(
        request: $request,
        next: function () use (&$didPass) {
            $didPass = true;
        },
    );
    expect($didPass)->toBeTrue();
});
