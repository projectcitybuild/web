<?php

namespace Tests\Unit\App\Services\PlayerBans;

use App\Exceptions\Http\ForbiddenException;
use App\Exceptions\Http\UnauthorisedException;
use App\Services\PlayerBans\Exceptions\MalformedTokenException;
use App\Services\PlayerBans\ServerKeyAuthService;
use Entities\Models\Eloquent\Server;
use Entities\Models\Eloquent\ServerKey;
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
        $server = Server::factory()->create();
        // given...
        ServerKey::create([
            'server_id' => $server->getKey(),
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
