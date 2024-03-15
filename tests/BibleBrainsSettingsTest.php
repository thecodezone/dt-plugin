<?php

namespace Tests;

use CodeZone\Bible\Exceptions\BibleBrainsException;
use CodeZone\Bible\GuzzleHttp\Psr7\Response as Psr7Response;
use CodeZone\Bible\Illuminate\Http\Client\RequestException;
use CodeZone\Bible\Illuminate\Http\Client\Response;
use CodeZone\Bible\Illuminate\Http\Request;
use CodeZone\Bible\Services\BibleBrains\Api\Bibles;
use function CodeZone\Bible\container;
use function CodeZone\Bible\get_plugin_option;

require "vendor-scoped/autoload.php";

/**
 * Class BibleBrainsSettingsTest
 *
 * This class is responsible for testing the BibleBrains settings page.
 *
 * @test
 */
class BibleBrainsSettingsTest extends TestCase {
	/**
	 * Test that the BibleBrains settings page loads.
	 * @test
	 */
	public function it_loads() {
		set_current_screen( 'toplevel_page_bible-plugin' );
		$user = $this->factory()->user->create( [
			'role' => 'administrator',
		] );
		wp_set_current_user( $user );

		$response = $this->get( '/wp-admin/admin.php?page=bible-plugin&tab=bible', [
			'page' => 'bible-plugin',
			'tab'  => 'bible'
		] );

		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertStringContainsString( 'bible_brains_key', $response->getContent() );
	}


	/**
	 * Test that the BibleBrains settings page requires a nonce.
	 * @test
	 */
	public function it_validates_the_nonce() {
		$user = $this->factory()->user->create( [
			'role' => 'administrator',
		] );
		wp_set_current_user( $user );

		$response = $this->post( 'bible/api/bible-brains', [
			'bible_plugin_languages'   => 'eng',
			'bible_plugin_language'    => 'eng',
			'bible_plugin_bibles'      => 'ENGKJV',
			'bible_plugin_bible'       => 'ENGKJV',
			'bible_plugin_media_types' => 'audio,video,text',
		] );

		$this->assertEquals( 400, $response->getStatusCode() );
	}

	/**
	 * Test that the BibleBrains settings page validates the submission.
	 * @test
	 */
	public function it_validates() {
		$user = $this->factory()->user->create( [
			'role' => 'administrator',
		] );
		wp_set_current_user( $user );

		$response = $this->post( 'bible/api/bible-brains', [
			'languages'   => null,
			'language'    => 'eng',
			'bibles'      => 'ENGKJV',
			'bible'       => 'ENGKJV',
			'media_types' => 'audio,video,text',
            ], [
			'X-WP-Nonce' => wp_create_nonce( 'bible-plugin' ),
		] );

		$data = json_decode( $response->getContent(), true );
		$this->assertEquals( 400, $response->getStatusCode() );
		$this->assertArrayHasKey( 'errors', $data );
		$this->assertArrayHasKey( 'languages', $data['errors'] );
		$this->assertArrayNotHasKey( 'language', $data['errors'] );
	}

	/**
	 * Test that the BibleBrains settings page saves the settings.
	 * @test
	 */
	public function it_saves() {
		$user = $this->factory()->user->create( [
			'role' => 'administrator',
		] );

		wp_set_current_user( $user );

		$payload = [
			'languages'   => 'languages',
			'language'    => 'language',
			'bibles'      => 'ENGKJV',
			'media_types' => 'audio,video,text',
		];

		$response = $this->post( 'bible/api/bible-brains', $payload, [
			'X-WP-Nonce' => wp_create_nonce( 'bible-plugin' ),
		] );

		$this->assertEquals( 200, $response->getStatusCode() );

		foreach ( $payload as $key => $value ) {
			$this->assertEquals( $value, get_plugin_option( $key ) );
		}
	}

	/**
	 * @test
	 */
	public function it_handles_api_key_failure() {
		$user = $this->factory()->user->create( [
			'role' => 'administrator',
		] );

		wp_set_current_user( $user );

		$payload = [
			'bible_brains_key' => 'fake_key'
		];

		$response = $this->post( 'bible/api/bible-brains/key', $payload, [
			'X-WP-Nonce' => wp_create_nonce( 'bible-plugin' ),
		] );

		$this->assertEquals( 401, $response->getStatusCode() );

		$data = json_decode( $response->getContent(), true );

		$this->assertArrayHasKey( 'errors', $data );
		$this->assertArrayHasKey( 'bible_brains_key', $data['errors'] );
	}

	/**
	 * @test
	 */
	public function it_handles_api_key_success() {
		$this->factory()->user->create( [
			'role' => 'administrator',
		] );

		$payload = [
			'bible_brains_key' => TBP_BIBLE_BRAINS_KEY
		];

		$response = $this->post( 'bible/api/bible-brains/key', $payload, [
			'X-WP-Nonce' => wp_create_nonce( 'bible-plugin' ),
		] );

		$this->assertEquals( 200, $response->getStatusCode() );

		$data = json_decode( $response->getContent(), true );

		$this->assertArrayHasKey( 'success', $data );
		$this->assertArrayNotHasKey( 'errors', $data );
		$this->assertArrayNotHasKey( 'error', $data );
	}
}
