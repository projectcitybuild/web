<?php
namespace Tests\Unit;

use Tests\TestCase;
use App\Core\Helpers\MorphMapHelpers;
use Illuminate\Database\Eloquent\Relations\Relation;

class MorphMapHelpers_Test extends TestCase {

    protected function setUp() {
        // clear morph map on each run
        Relation::morphMap([], false);
    }

    function testGetMorphMapOf_whenValid_returnsKey() {
        Relation::morphMap([
            'test_key' => 'test_value',
        ]);

        $test = new MorphMapHelpers();
        $result = $test->getMorphKeyOf('test_value');

        $this->assertEquals('test_key', $result);
    }

    function testGetMorphMapOf_whenValueDoesntExist_throwsException() {
        $this->expectException(\Exception::class);

        $test = new MorphMapHelpers();
        $result = $test->getMorphKeyOf('test_value');
    }
    
}
