<?php

namespace Tests;

use CodeZone\Bible\Services\Translations;
use function CodeZone\Bible\container;

require "vendor-scoped/autoload.php";

/**
 * Class BibleBrainsSettingsTest
 *
 * This class is responsible for testing the BibleBrains settings page.
 *
 * @test
 */
class TranslatorTest extends TestCase {
	/**
	 * Test that the BibleBrains settings page loads.
	 * @test
	 */
	public function it_translates() {
		$translator = container()->make( Translations::class );
		$this->assertEquals( 'Hello World', $translator->translate( 'Hello World' ) );
	}
}
