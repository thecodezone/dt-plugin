<?php

namespace Tests;

use CodeZone\Bible\Illuminate\Support\Str;
use CodeZone\Bible\Services\BibleBrains\Services\Bibles;
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
class BibleTest extends TestCase {
	/**
	 * @test
	 */
	public function it_can_fetch_a_bible() {
		$response = $this->get( 'bible/api/bibles/ENGESV' );
		$this->assertEquals( 200, $response->status() );
		$result = json_decode( $response->getContent(), true );
		$this->assertEquals( 'ENGESV', $result['data']['abbr'] );
	}

	/**
	 * Test that the BibleBrains settings page loads.
	 * @test
	 */
	public function it_can_fetch_language_options() {
		$response = $this->get( 'bible/api/bibles/options', [ 'limit' => 2 ] );
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
		$languages = container()->make( Bibles::class );
		$response  = $languages->search( 'New King James Version' );
		$this->assertEquals( 200, $response->status() );
		$result = $response->json();
		$this->assertGreaterThan( 0, count( $result['data'] ) );
	}

	/**
	 * @test
	 */
	public function it_can_fetch_bibles_for_a_language() {
		$response = $this->get( 'bible/api/languages/6414/bibles' );
		$this->assertEquals( 200, $response->status() );
		$result = json_decode( $response->getContent(), true );
		$this->assertGreaterThan( 0, count( $result['data'] ) );
	}
}
