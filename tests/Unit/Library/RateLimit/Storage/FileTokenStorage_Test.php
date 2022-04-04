<?php

namespace Tests\Library\RateLimit;

use Library\RateLimit\Storage\FileTokenStorage;
use Library\RateLimit\TokenState;
use Tests\TestCase;

class FileTokenStorage_Test extends TestCase
{
    private $path;

    protected function setUp(): void
    {
        parent::setUp();

        $this->path = storage_path('test_file.ratelimit');
    }

    public function tearDown(): void
    {
        parent::tearDown();

        if (file_exists($this->path)) {
            unlink($this->path);
        }
    }

    public function testSerializeCreatesFile()
    {
        // given...
        $state = new TokenState(10, time());
        $storage = new FileTokenStorage($this->path, 10);

        // when...
        $storage->serialize($state);

        // expect...
        $this->assertFileExists($this->path);
    }

    public function testCanDeserialize()
    {
        // given...
        $state = new TokenState(15, time());
        $storage = new FileTokenStorage($this->path, 15);

        // when...
        $storage->serialize($state);
        $deserialized = $storage->deserialize();

        // expect...
        $this->assertEquals($state->tokensAvailable, $deserialized->tokensAvailable);
        $this->assertEquals($state->lastConsumeTime, $deserialized->lastConsumeTime);
    }
}
