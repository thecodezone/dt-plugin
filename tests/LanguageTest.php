<?php

namespace Tests;

use CodeZone\Bible\Services\BibleBrains\Services\Languages;
use function CodeZone\Bible\container;

require "vendor-scoped/autoload.php";

/**
 * Class BibleBrainsSettingsTest
 *
 * This class is responsible for testing the BibleBrains settings page.
 *
 * @test
 */
class LanguageTest extends TestCase {
	/**
	 * Test that the BibleBrains settings page loads.
	 * @test
	 */
	public function it_can_fetch_language_options() {
		$response = $this->get( 'bible/api/languages/options', [ 'limit' => 2 ] );
		$this->assertEquals( 200, $response->status() );
		$result = json_decode( $response->getContent(), true );
		$this->assertEquals( 2, count( $result['data'] ) );
		foreach ( json_decode( $response->getContent(), true )['data'] as $language ) {
			$this->assertArrayHasKey( 'value', $language );
			$this->assertArrayHasKey( 'label', $language );
		}
	}

	/**
	 * Test that the BibleBrains settings page loads.
	 * @test
	 */
	public function it_can_search() {
		$languages = container()->make( Languages::class );
		$response  = $languages->search( 'Eng' );
		$this->assertEquals( 200, $response->status() );
	}

	/**
	 * Test that the BibleBrains settings page loads.
	 * @skip-test
	 */
	public function it_can_get_all() {
		$languages = container()->make( Languages::class );
		$response  = $languages->all_pages();
		$this->assertGreaterThan( 150, count( $response ) );
	}
}
