<?php
namespace Tests\Services;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Domains\Services\PlayerBans\ServerKeyAuthService;
use Domains\Services\PlayerBans\Exceptions\MalformedTokenException;
use Application\Exceptions\ForbiddenException;
use Application\Exceptions\UnauthorisedException;
use Domains\Modules\ServerKeys\Models\ServerKey;

class ServerKeyAuthService_Test extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;

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