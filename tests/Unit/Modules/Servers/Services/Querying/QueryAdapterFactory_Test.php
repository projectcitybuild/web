<?php
namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Modules\Servers\Services\Querying\QueryAdapterFactory;
use App\Modules\Servers\GameTypeEnum;
use App\Modules\Servers\Services\Querying\GameAdapters\MinecraftQueryAdapter;

class QueryAdapterFactory_Test extends TestCase {

    
    public function setUp() {
        parent::setUp();
    }

    /**
     * Tests that an unknown server type enum results in an exception
     *
     * @return void
     */
    public function testGetAdapter_whenUnknownGame_throwsException() {
        $this->expectException(\Exception::class);

        $factory = new QueryAdapterFactory();
        $factory->getAdapter(0);
    }

    /**
     * Tests that when given a Minecraft server type, returns a Minecraft query adapter
     *
     * @return void
     */
    public function testGetAdapter_whenMinecraftServer_returnsMinecraftAdapter() {
        $factory = new QueryAdapterFactory();
        $test = $factory->getAdapter(GameTypeEnum::Minecraft);

        $this->assertInstanceOf(MinecraftQueryAdapter::class, $test);
    }
    

}
