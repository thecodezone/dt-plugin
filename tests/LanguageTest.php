<?php

namespace Tests;

use CodeZone\Bible\Illuminate\Support\Str;
use CodeZone\Bible\Services\BibleBrains\Api\Languages;
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
	 * @test
	 */
	public function it_can_fetch_a_language() {
		$response = $this->get( 'bible/api/languages/6414' );
		$this->assertEquals( 200, $response->status() );
		$result = json_decode( $response->getContent(), true );
		$this->assertEquals( '6414', $result['data']['id'] );
	}

	/**
	 * @test
	 */
	public function it_filters_language_variants_from_options() {
		$languages              = container()->make( Languages::class );
		$result                 = collect( $languages->as_options(
			collect( collect( $languages->search( 'english' ) )->get( 'data' ) )
		) );
		$containing_english     = $result->filter( function ( $language ) {
			return Str::contains( $language['itemText'], 'English' );
		} );
		$containing_parenthesis = $result->filter( function ( $language ) {
			return Str::contains( $language['itemText'], '(' );
		} );
		$this->assertEquals( 1, $containing_english->count() );
		$this->assertEquals( 0, $containing_parenthesis->count() );
	}

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
			$this->assertArrayHasKey( 'itemText', $language );
		}
	}

	/**
	 * Test that the BibleBrains settings page loads.
	 * @test
	 */
	public function it_can_search() {
		$languages = container()->make( Languages::class );
		$result    = $languages->search( 'Spanish' );
		$this->assertGreaterThan( 0, count( $result['data'] ) );
	}
}
