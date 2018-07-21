<?php
namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Log\Logger;
use App\Modules\Bans\Services\BanAuthorisationService;
use App\Support\Exceptions\ServerException;
use App\Modules\ServerKeys\Models\ServerKey;
use App\Modules\Bans\Models\GameBan;

class BanAuthorisationService_Test extends TestCase
{
    private $loggerMock;

    private $keyMock;

    public function setUp()
    {
        $this->loggerMock = $this->getMockBuilder(Logger::class)
            ->disableOriginalConstructor()
            ->getMock();
    }


    /**
     * A global ban can be created when a server key
     * has global ban permission
     *
     * @return void
     */
    public function testIsAllowedToBan_whenAllowedGlobalBan_returnsTrue()
    {
        $test = new BanAuthorisationService($this->loggerMock);
        
        $key = new ServerKey();
        $key->can_global_ban = true;
        $result = $test->isAllowedToBan(true, $key);

        $this->assertTrue($result);
    }

    /**
     * A global ban cannot be created when the server key
     * does not have global ban permission
     *
     * @return void
     */
    public function testIsAllowedToBan_whenDisallowedGlobalBan_returnsFalse()
    {
        $test = new BanAuthorisationService($this->loggerMock);
        
        $key = new ServerKey();
        $key->can_global_ban = false;
        $result = $test->isAllowedToBan(true, $key);

        $this->assertFalse($result);
    }

    /**
     * A local server ban can be created when the server key
     * has local ban permission
     *
     * @return void
     */
    public function testIsAllowedToBan_whenAllowedLocalBan_returnsTrue()
    {
        $test = new BanAuthorisationService($this->loggerMock);
        
        $key = new ServerKey();
        $key->can_local_ban = true;
        $result = $test->isAllowedToBan(false, $key);

        $this->assertTrue($result);
    }

    /**
     * A local server ban cannot be created when the server
     * key does not have local ban permission
     *
     * @return void
     */
    public function testIsAllowedToBan_whenDisallowedLocalBan_returnFalse()
    {
        $test = new BanAuthorisationService($this->loggerMock);
        
        $key = new ServerKey();
        $key->can_local_ban = false;
        $result = $test->isAllowedToBan(false, $key);

        $this->assertFalse($result);
    }


    /**
     * A server key can unban a global ban when it has
     * global ban permission
     *
     * @return void
     */
    public function testIsAllowedToUnban_whenGlobalBan_andCanGlobalBan_returnsTrue()
    {
        $test = new BanAuthorisationService($this->loggerMock);

        $key = new ServerKey();
        $key->can_global_ban = true;

        $ban = new GameBan();
        $ban->is_global_ban = true;

        $result = $test->isAllowedToUnban($ban, $key);

        $this->assertTrue($result);
    }

    /**
     * A global ban cannot be unbanned when the server key
     * does not have global ban permission
     *
     * @return void
     */
    public function testIsAllowedToUnban_whenGlobalBan_andCannotGlobalBan_returnsFalse()
    {
        $test = new BanAuthorisationService($this->loggerMock);

        $key = new ServerKey();
        $key->can_global_ban = false;

        $ban = new GameBan();
        $ban->is_global_ban = true;

        $result = $test->isAllowedToUnban($ban, $key);

        $this->assertFalse($result);
    }

    /**
     * A ban created on the same server as the server key's server,
     * with a server key that has local ban permission, should be allowed
     *
     * @return void
     */
    public function testIsAllowedToUnban_whenLocalBan_andSameServer_andCanLocalBan_returnsTrue()
    {
        $test = new BanAuthorisationService($this->loggerMock);

        $key = new ServerKey();
        $key->can_global_ban = false;
        $key->can_local_ban = true;
        $key->server_id = 1;

        $ban = new GameBan();
        $ban->is_global_ban = false;
        $ban->server_id = 1;

        $result = $test->isAllowedToUnban($ban, $key);

        $this->assertTrue($result);
    }
    
    /**
     * A server key must have local ban permission to even
     * unban its own server's bans
     *
     * @return void
     */
    public function testIsAllowedToUnban_whenLocalBan_andSameServer_butCannotLocalBan_returnsFalse()
    {
        $test = new BanAuthorisationService($this->loggerMock);

        $key = new ServerKey();
        $key->can_global_ban = false;
        $key->can_local_ban = false;
        $key->server_id = 1;

        $ban = new GameBan();
        $ban->is_global_ban = false;
        $ban->server_id = 1;

        $result = $test->isAllowedToUnban($ban, $key);

        $this->assertFalse($result);
    }

    /**
     * A server key with only local ban permission cannot
     * unban a different server's local ban
     *
     * @return void
     */
    public function testIsAllowedToUnban_whenLocalBan_andCanLocalBan_butDifferentServer_returnsFalse()
    {
        $test = new BanAuthorisationService($this->loggerMock);

        $key = new ServerKey();
        $key->can_global_ban = false;
        $key->can_local_ban = true;
        $key->server_id = 1;

        $ban = new GameBan();
        $ban->is_global_ban = false;
        $ban->server_id = 2;

        $result = $test->isAllowedToUnban($ban, $key);

        $this->assertFalse($result);
    }

    /**
     * A server key can unban a different server's local ban
     * if the key has global ban permission
     *
     * @return void
     */
    public function testIsAllowedToUnban_whenLocalBan_butDifferentServer_andCanGlobalBan_returnsTrue()
    {
        $test = new BanAuthorisationService($this->loggerMock);

        $key = new ServerKey();
        $key->can_global_ban = true;
        $key->can_local_ban = true;
        $key->server_id = 1;

        $ban = new GameBan();
        $ban->is_global_ban = false;
        $ban->server_id = 2;

        $result = $test->isAllowedToUnban($ban, $key);

        $this->assertTrue($result);
    }
}
