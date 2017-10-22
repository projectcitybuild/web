<?php
namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\ParameterBag;
use App\Routes\Api\Middleware\ServerTokenValidate;
use App\Modules\Servers\Repositories\ServerKeyRepository;
use App\Modules\Servers\Repositories\ServerKeyTokenRepository;
use App\Modules\Servers\Models\ServerKeyToken;
use App\Modules\Servers\Models\ServerKey;

// this really should've been an integration test... *facepalm*
class ServerTokenValidate_Test extends TestCase {

    private $requestMock;
    private $headerMock;
    private $attributeMock;
    private $serverKeyMock;
    private $serverKeyTokenMock;

    public function setUp() {
        parent::setUp();

        $this->serverKeyMock = $this->getMockBuilder(ServerKeyRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->serverKeyTokenMock = $this->getMockBuilder(ServerKeyTokenRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->requestMock = $this->getMockBuilder(Request::class)
            ->getMock();

        // mock Request's internal call to HeaderBag so we can control the header returned
        $this->headerMock = $this->getMockBuilder(HeaderBag::class)->getMock();
        $this->requestMock->headers = $this->headerMock;
        
        // mock Request's internal call to ParameterBag
        $this->attributeMock = $this->getMockBuilder(ParameterBag::class)->getMock();
        $this->requestMock->attributes = $this->attributeMock;
    }

    /**
     * Asserts an exception is thrown when no Authorization header present
     *
     * @return void
     */
    public function testHandle_whenNoAuthHeader_throwsException() {
        // $this->expectExceptionCode(401);
        $this->expectException(\Exception::class);

        $this->headerMock
            ->method('get')
            ->will($this->returnValue(null));

        $middleware = new ServerTokenValidate($this->serverKeyTokenMock, $this->serverKeyMock);
        $middleware->handle($this->requestMock, function() {});
    }

    /**
     * Asserts an exception is thrown when Authorization header is not of 'Bearer X' format
     *
     * @return void
     */
    public function testHandle_whenBadAuthHeaderFormat_throwsException() {
        // $this->expectExceptionCode(400);
        $this->expectException(\Exception::class);
        
        $this->headerMock
            ->method('get')
            ->will($this->returnValue('bad_format'));

        $middleware = new ServerTokenValidate($this->serverKeyTokenMock, $this->serverKeyMock);
        $middleware->handle($this->requestMock, function() {});
    }

    /**
     * Asserts that a token in the correct format, but not registered in our
     * database will throw an exception
     *
     * @return void
     */
    public function testHandle_whenNonExistentToken_throwsException() {
        // $this->expectExceptionCode(403);
        $this->expectException(\Exception::class);
        
        $this->headerMock
            ->method('get')
            ->will($this->returnValue('Bearer non_existent_token'));

        $middleware = new ServerTokenValidate($this->serverKeyTokenMock, $this->serverKeyMock);
        $middleware->handle($this->requestMock, function() {});
    }

    /**
     * Asserts that a valid-format token that is blacklisted in our database
     * will throw an exception
     *
     * @return void
     */
    public function testHandle_whenBlacklistedToken_throwsException() {
        // $this->expectExceptionCode(403);
        $this->expectException(\Exception::class);
        
        $this->headerMock
            ->method('get')
            ->will($this->returnValue('Bearer blacklisted_token'));

        $blacklistedToken = new ServerKeyToken();
        $blacklistedToken->is_blacklisted = true;

        $this->serverKeyTokenMock
            ->method('getByToken')
            ->will($this->returnValue($blacklistedToken));

        $middleware = new ServerTokenValidate($this->serverKeyTokenMock, $this->serverKeyMock);
        $middleware->handle($this->requestMock, function() {});
    }

    /**
     * Asserts that when a valid token with a non-existent server key
     * will throw an exception
     *
     * @return void
     */
    public function testHandle_whenNonExistentServerKey_throwsException() {
        $this->expectException(\Exception::class);
        
        $this->headerMock
            ->method('get')
            ->will($this->returnValue('Bearer valid_token'));

        $serverToken = new ServerKeyToken(['server_key_id' => 1]);

        $this->serverKeyTokenMock
            ->method('getByToken')
            ->will($this->returnValue($serverToken));

        $this->serverKeyMock
            ->method('getById')
            ->will($this->returnValue(null));

        $middleware = new ServerTokenValidate($this->serverKeyTokenMock, $this->serverKeyMock);
        $middleware->handle($this->requestMock, function() {});
    }

    /**
     * Asserts that a valid token will pass through the whole middleware
     *
     * @return void
     */
    public function testHandle_whenValidToken_noExceptions() {
        $this->headerMock
            ->method('get')
            ->will($this->returnValue('Bearer valid_token'));

        $serverToken = new ServerKeyToken(['server_key_id' => 1]);

        $this->serverKeyTokenMock
            ->method('getByToken')
            ->will($this->returnValue($serverToken));

        $this->serverKeyMock
            ->method('getById')
            ->will($this->returnValue(new ServerKey));

        $this->attributeMock
            ->expects($this->once())
            ->method('add');

        $middleware = new ServerTokenValidate($this->serverKeyTokenMock, $this->serverKeyMock);
        $middleware->handle($this->requestMock, function() {});
    }
}
