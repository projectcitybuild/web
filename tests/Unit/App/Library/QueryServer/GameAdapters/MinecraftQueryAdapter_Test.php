<?php

namespace Tests\Library\QueryServer\GameAdapters;

use App\Library\QueryServer\GameAdapters\MinecraftQueryAdapter;
use Tests\TestCase;
use xPaw\MinecraftQuery;
use xPaw\MinecraftQueryException;

class MinecraftQueryAdapter_Test extends TestCase
{
    private $queryMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->queryMock = $this->getMockBuilder(MinecraftQuery::class)
            ->getMock();
    }

    public function testReturnsCorrectStats()
    {
        // given...
        $this->queryMock
            ->expects($this->once())
            ->method('GetInfo')
            ->willReturn([
                'Players' => 10,
                'MaxPlayers' => 20,
            ]);

        $adapter = new MinecraftQueryAdapter($this->queryMock);

        // when...
        $status = $adapter->query('192.168.0.1', '25565');

        // expect...
        $this->assertEquals(10, $status->getNumOfPlayers());
        $this->assertEquals(20, $status->getNumOfSlots());
    }

    public function testReturnsPlayers()
    {
        // given...
        $this->queryMock
            ->expects($this->once())
            ->method('GetPlayers')
            ->willReturn(['test_player']);

        $adapter = new MinecraftQueryAdapter($this->queryMock);

        // when...
        $status = $adapter->query('192.168.0.1', '25565');

        // expect...
        $this->assertEquals(['test_player'], $status->getPlayerList());
    }

    public function testReturnsOnline()
    {
        // given...
        $this->queryMock
            ->expects($this->once())
            ->method('GetInfo')
            ->willReturn([
                'Players' => 10,
                'MaxPlayers' => 20,
            ]);

        $adapter = new MinecraftQueryAdapter($this->queryMock);

        // when...
        $status = $adapter->query('192.168.0.1', '25565');

        // expect...
        $this->assertTrue($status->isOnline());
    }

    public function testReturnsOffline()
    {
        // given...
        $this->queryMock
            ->expects($this->once())
            ->method('GetInfo')
            ->will($this->throwException(new MinecraftQueryException));

        $adapter = new MinecraftQueryAdapter($this->queryMock);

        // when...
        $status = $adapter->query('192.168.0.1', '25565');

        // expect...
        $this->assertFalse($status->isOnline());
    }

    public function testOfflineReturnsZeroPlayers()
    {
        // given...
        $this->queryMock
            ->expects($this->once())
            ->method('GetInfo')
            ->will($this->throwException(new MinecraftQueryException));

        $adapter = new MinecraftQueryAdapter($this->queryMock);

        // when...
        $status = $adapter->query('192.168.0.1', '25565');

        // expect...
        $this->assertFalse($status->isOnline());
        $this->assertEquals(0, $status->getNumOfPlayers());
    }
}
