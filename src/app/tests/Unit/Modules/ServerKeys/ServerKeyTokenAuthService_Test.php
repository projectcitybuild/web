<?php
namespace Tests\Unit;

use Tests\TestCase;
use App\Modules\ServerKeys\Services\ServerKeyTokenAuthService;
use App\Modules\ServerKeys\Exceptions\MalformedTokenException;
use App\Modules\ServerKeys\Repositories\ServerKeyTokenRepository;
use App\Modules\ServerKeys\Models\ServerKeyToken;
use App\Support\Exceptions\ForbiddenException;
use App\Modules\ServerKeys\Models\ServerKey;
use App\Modules\ServerKeys\Exceptions\ExpiredTokenException;
use App\Modules\ServerKeys\Exceptions\UnauthorisedTokenException;

class ServerKeyTokenAuthService_Test extends TestCase {

    private $tokenRepositoryMock;

    public function setUp() {
        $this->tokenRepositoryMock = $this->getMockBuilder(ServerKeyTokenRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
    }


    /**
     * An Authorization header in the format of 'Bearer <token>'
     * should return the <token>
     *
     * @return void
     */
    function testGetAuthHeader_validHeader_succeeds() {
        $test = new ServerKeyTokenAuthService($this->tokenRepositoryMock);
        $result = $test->getAuthHeader('Bearer a_valid_token');

        $this->assertEquals('a_valid_token', $result);
    }

    /**
     * An Authorization header not in the format of 'Bearer <token>'
     * should throw an exception
     *
     * @return void
     */
    function testGetAuthHeader_badHeader_throwsException() {
        $this->expectException(MalformedTokenException::class);

        $test = new ServerKeyTokenAuthService($this->tokenRepositoryMock);
        $result = $test->getAuthHeader('invalid_token');
    }

    /**
     * An empty Authorization header should throw an exception
     *
     * @return void
     */
    function testGetAuthHeader_emptyHeader_throwsException() {
        $this->expectException(ForbiddenException::class);

        $test = new ServerKeyTokenAuthService($this->tokenRepositoryMock);
        $result = $test->getAuthHeader('');
    }

    /**
     * A null Authorization header should throw an exception
     *
     * @return void
     */
    function testGetAuthHeader_nullHeader_throwsException() {
        $this->expectException(ForbiddenException::class);

        $test = new ServerKeyTokenAuthService($this->tokenRepositoryMock);
        $result = $test->getAuthHeader(null);
    }

    /**
     * When a token is valid, it should return the associated
     * server key
     *
     * @return void
     */
    function testGetServerKey_whenValidToken_returnsKey() {
        $token = new ServerKeyToken();
        $token->is_blacklisted = false;
        $token->serverKey = new ServerKey();

        $this->tokenRepositoryMock
            ->method('getByToken')
            ->will($this->returnValue($token));

        $test = new ServerKeyTokenAuthService($this->tokenRepositoryMock);
        $result = $test->getServerKey('valid_token');

        $this->assertNotNull($result);
    }

    /**
     * When a token has expired, it should throw an exception
     *
     * @return void
     */
    function testGetServerKey_whenExpiredToken_throwsException() {
        $this->expectException(ExpiredTokenException::class);

        $token = new ServerKeyToken();
        $token->is_blacklisted = true;

        $this->tokenRepositoryMock
            ->method('getByToken')
            ->will($this->returnValue($token));

        $test = new ServerKeyTokenAuthService($this->tokenRepositoryMock);
        $result = $test->getServerKey('expired_token');
    }

    /**
     * When a found token doesn't have a server key linked to it,
     * it should throw an exception
     *
     * @return void
     */
    function testGetServerKey_whenTokenMissingKey_throwsException() {
        $this->expectException(UnauthorisedTokenException::class);

        $token = new ServerKeyToken();
        $token->is_blacklisted = false;
        $token->serverKey = null;

        $this->tokenRepositoryMock
            ->method('getByToken')
            ->will($this->returnValue($token));

        $test = new ServerKeyTokenAuthService($this->tokenRepositoryMock);
        $result = $test->getServerKey('bad_token');
    }

    /**
     * When we can't find the given token in our db, the token is
     * invalid and therefore should throw an exception
     *
     * @return void
     */
    function testGetServerKey_whenTokenNotFound_throwsException() {
        $this->expectException(UnauthorisedTokenException::class);

        $this->tokenRepositoryMock
            ->method('getByToken')
            ->will($this->returnValue(null));

        $test = new ServerKeyTokenAuthService($this->tokenRepositoryMock);
        $result = $test->getServerKey('bad_token');
    }
    
}
