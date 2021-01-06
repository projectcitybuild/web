<?php

namespace Tests\Services;

use App\Entities\ServerKeys\Models\ServerKey;
use App\Exceptions\Http\ForbiddenException;
use App\Exceptions\Http\UnauthorisedException;
use App\Services\PlayerBans\Exceptions\MalformedTokenException;
use App\Services\PlayerBans\ServerKeyAuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServerKeyAuthService_Test extends TestCase
{
    use RefreshDatabase;

    public function testMissingHeader_throwsException()
    {
        $service = resolve(ServerKeyAuthService::class);
        $this->expectException(ForbiddenException::class);
        $service->getServerKey(null);
    }

    public function testEmptyHeader_throwsException()
    {
        $service = resolve(ServerKeyAuthService::class);
        $this->expectException(ForbiddenException::class);
        $service->getServerKey(null);
    }

    public function testMalformedHeader_throwsException()
    {
        $service = resolve(ServerKeyAuthService::class);
        $this->expectException(MalformedTokenException::class);
        $service->getServerKey('bad_format');
    }

    public function testUnregisteredKey_throwsException()
    {
        $service = resolve(ServerKeyAuthService::class);
        $this->expectException(UnauthorisedException::class);
        $service->getServerKey('Bearer unregistered_key');
    }

    public function testRegisteredKey()
    {
        // given...
        ServerKey::create([
            'server_id' => 1,
            'token' => 'valid_key',
            'can_local_ban' => true,
            'can_global_ban' => true,
            'can_warn' => true,
        ]);
        $service = resolve(ServerKeyAuthService::class);

        // when...
        $serverKey = $service->getServerKey('Bearer valid_key');

        // expect...
        $this->assertNotNull($serverKey);
        $this->assertInstanceOf(ServerKey::class, $serverKey);
    }
}
