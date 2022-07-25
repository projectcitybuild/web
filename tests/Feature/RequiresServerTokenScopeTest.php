<?php

namespace Tests\Feature;

use App\Http\Middleware\RequiresServerTokenScope;
use Domain\ServerTokens\ScopeKey;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\E2ETestCase;

class RequiresServerTokenScopeTest extends E2ETestCase
{
    public function test_throws_unauthorized_exception_if_token_missing()
    {
        try {
            $middleware = new RequiresServerTokenScope();
            $middleware->handle(
                request: Request::create(uri: 'test'),
                next: function() {},
                scope: ScopeKey::BAN_LOOKUP->value,
            );
        }
        catch (HttpException $e) {}

        $this->assertEquals(
            expected: new HttpException(401),
            actual: $e,
        );
    }

    public function test_throws_unauthorized_exception_if_token_malformed()
    {
        $invalidTokens = ['invalid', 'Bearer'];

        foreach ($invalidTokens as $invalidToken) {
            $request = Request::create(uri: 'test');
            $request->headers->set(key: 'Authorization', values: $invalidToken);

            try {
                $middleware = new RequiresServerTokenScope();
                $middleware->handle(
                    request: $request,
                    next: function() {},
                    scope: ScopeKey::BAN_LOOKUP->value,
                );
            }
            catch (HttpException $e) {}

            $this->assertEquals(
                expected: new HttpException(401),
                actual: $e,
            );
            $this->assertEquals(
                expected: $invalidToken,
                actual: $request->headers->get('Authorization'),
            );
        }
    }

    public function test_throws_unauthorized_exception_if_token_not_registered()
    {
        $request = Request::create(uri: 'test');
        $request->headers->set(key: 'Authorization', values: 'Bearer some_token');

        try {
            $middleware = new RequiresServerTokenScope();
            $middleware->handle(
                request: $request,
                next: function () {},
                scope: ScopeKey::BAN_LOOKUP->value,
            );
        }
        catch (HttpException $e) {}

        $this->assertEquals(
            expected: new HttpException(401),
            actual: $e,
        );
        $this->assertEquals(
            expected: 'Bearer some_token',
            actual: $request->headers->get('Authorization'),
        );
    }

    public function test_throws_forbidden_exception_if_scope_not_allowed_by_key()
    {
        $token = $this->createServerToken();

        $request = Request::create(uri: 'test');
        $request->headers->set(key: 'Authorization', values: 'Bearer '.$token->token);

        try {
            $middleware = new RequiresServerTokenScope();
            $middleware->handle(
                request: $request,
                next: function () {},
                scope: ScopeKey::BAN_LOOKUP->value,
            );
        } catch (HttpException $e) {
        }

        $this->assertEquals(
            expected: new HttpException(403),
            actual: $e,
        );
        $this->assertEquals(
            expected: 'Bearer '.$token->token,
            actual: $request->headers->get('Authorization'),
        );
    }

    public function test_valid_token_passes_onto_next_middleware()
    {
        $token = $this->createServerToken();
        $this->authoriseTokenFor(ScopeKey::BAN_LOOKUP);

        $request = Request::create(uri: 'test');
        $request->headers->set(key: 'Authorization', values: 'Bearer '.$token->token);

        $didPass = false;
        $middleware = new RequiresServerTokenScope();
        $middleware->handle(
            request: $request,
            next: function () use (&$didPass) {
                $didPass = true;
            },
            scope: ScopeKey::BAN_LOOKUP->value,
        );

        $this->assertTrue($didPass);
        $this->assertEquals(
            expected: 'Bearer '.$token->token,
            actual: $request->headers->get('Authorization'),
        );
        $this->assertEquals(
            expected: $token->getKey(),
            actual: $request->token->getKey(),
        );
    }
}
