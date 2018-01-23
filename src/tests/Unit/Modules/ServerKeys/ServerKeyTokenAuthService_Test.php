<?php
namespace Tests\Unit;

use Tests\TestCase;
use App\Modules\ServerKeys\Services\ServerKeyTokenAuthService;
use App\Modules\ServerKeys\Exceptions\MalformedTokenException;
use App\Modules\ServerKeys\Models\ServerKeyToken;

class ServerKeyTokenAuthService_Test extends TestCase {

    public function testGetAuthHeader_validHeader_succeeds() {
        $test = resolve(ServerKeyTokenAuthService::class);
        $result = $test->getAuthHeader('Bearer a_valid_token');

        $this->assertEquals('a_valid_token', $result);
    }

    public function testGetAuthHeader_badHeader_throwsException() {
        $this->assertThrows(MalformedTokenException::class);

        $test = resolve(ServerKeyTokenAuthService::class);
        $result = $test->getAuthHeader('invalid_token');
    }

    public function testGetAuthHeader_emptyHeader_throwsException() {
        $this->assertThrows(MalformedTokenException::class);

        $test = resolve(ServerKeyTokenAuthService::class);
        $result = $test->getAuthHeader('');
    }
    
}
