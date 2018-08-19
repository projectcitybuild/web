<?php
namespace Tests\Helpers;

use Tests\TestCase;
use Domains\Helpers\TokenHelpers;

class TokenHelpers_Test extends TestCase
{
    private $originalKey;

    protected function setUp()
    {
        parent::setUp();
        $this->originalKey = config('app.key');
    }

    protected function tearDown()
    {
        config(['app.key' => $this->originalKey]);
        parent::tearDown();
    }

    
    public function testCanGenerateToken()
    {
        // given...
        config(['app.key' => 'test_key']);
        
        // when...
        $token = TokenHelpers::generateToken('data_to_be_tokenized');
        
        // expect...
        $this->assertEquals('b3ff48dd70783b6c5bbf51f887e22d8f278ae1dbd1453844260d6cf703fdbaab', $token);
    }
}
