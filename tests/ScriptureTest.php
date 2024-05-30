<?php

namespace Tests;

use CodeZone\Bible\Services\BibleBrains\Api\Languages;
use CodeZone\Bible\Services\BibleBrains\Scripture;
use function CodeZone\Bible\container;

class ScriptureTest extends TestCase {
	/**
	 * @test
	 */
	public function it_can_query_by_reference() {
		$scripture = container()->make( Scripture::class );
		$result    = $scripture->by_reference( "John 3:16" );
		$this->assertEquals( $result['chapter'], 3 );
        $this->assertEquals( $result['verse_start'], 16 );
        $this->assertEquals( $result['verse_end'], 16 );
	}
}
