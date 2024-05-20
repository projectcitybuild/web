<?php

namespace Tests\Unit\Middleware;

use App\Http\Middleware\EnsureClientToken;
use App\Models\Eloquent\ClientToken;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Mockery\MockInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class EnsureClientTokenTest extends TestCase
{
    private \Closure $nextStub;

    protected function setUp(): void
    {
        parent::setUp();

        $this->nextStub = fn () => null;
    }

    public function test_no_header_throws_401(): void
    {
        $this->assertHttpCode(401, authHeader: null);
    }

    public function test_empty_header_throws_401(): void
    {
        $this->assertHttpCode(401, authHeader: '');
    }

    public function test_malformed_bearer_throws_401(): void
    {
        $this->assertHttpCode(401, authHeader: 'token');
        $this->assertHttpCode(401, authHeader: 'Bearer token token');
        $this->assertHttpCode(401, authHeader: 'bearer token');
        $this->assertHttpCode(401, authHeader: 'BEaReR token');
    }

    public function test_no_matching_client_token_throws_401(): void
    {
        $this->assertHttpCode(401, authHeader: 'Bearer token');
    }

    public function test_no_matching_scope_throws_403(): void
    {
        $rawToken = Str::uuid();
        $token = ClientToken::factory()
            ->token($rawToken)
            ->scoped('aaa')
            ->create();

        $this->assertTrue(
            ClientToken::where('id', $token->getKey())->exists(),
        );
        $this->assertHttpCode(
            code: 403,
            authHeader: 'Bearer '.$rawToken->toString(),
            scope: 'bbb',
        );
    }

    public function test_matching_scope_succeeds(): void
    {
        $rawToken = Str::uuid();
        ClientToken::factory()
            ->token($rawToken)
            ->scoped('minecraft')
            ->create();

        $proceeded = false;
        $next = function() use (&$proceeded) {
            $proceeded = true;
            return $this->mock(Response::class);
        };

        $request = $this->mockRequest(
            authHeader: 'Bearer '.$rawToken->toString(),
        );
        $middleware = new EnsureClientToken();
        $middleware->handle($request, $next, scope: 'minecraft');

        $this->assertTrue($proceeded);
    }

    public function test_matching_multi_scope_succeeds(): void
    {
        $rawToken = Str::uuid();
        ClientToken::factory()
            ->token($rawToken)
            ->scoped('test,minecraft')
            ->create();

        $proceeded = false;
        $next = function() use (&$proceeded) {
            $proceeded = true;
            return $this->mock(Response::class);
        };

        $request = $this->mockRequest(
            authHeader: 'Bearer '.$rawToken->toString(),
        );
        $middleware = new EnsureClientToken();
        $middleware->handle($request, $next, scope: 'minecraft');

        $this->assertTrue($proceeded);
    }

    private function mockRequest(?string $authHeader): Request|MockInterface
    {
        return $this->mock(Request::class, function (MockInterface $mock) use ($authHeader) {
            $mock->shouldReceive('header')
                ->with('Authorization')
                ->andReturn($authHeader);
        });
    }

    private function assertHttpCode(
        int $code,
        ?string $authHeader,
        string $scope = 'scope',
    ) {
        $request = $this->mockRequest($authHeader);

        try {
            $middleware = new EnsureClientToken();
            $middleware->handle($request, $this->nextStub, $scope);
        } catch (\Throwable $e) {}

        $this->assertEquals(new HttpException(statusCode: $code), $e);
    }
}
