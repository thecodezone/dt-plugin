<?php

namespace Tests;

require "vendor-scoped/autoload.php";

/**
 * Class BibleBrainsSettingsTest
 *
 * This class is responsible for testing the BibleBrains settings page.
 *
 * @test
 */
class CustomizeSettingsTest extends TestCase {
	/**
	 * Test that the BibleBrains settings page loads.
	 * @test
	 */
	public function it_shows() {
		set_current_screen( 'toplevel_page_bible-plugin' );
		$user = $this->factory()->user->create( [
			'role' => 'administrator',
		] );
		wp_set_current_user( $user );
		$response = $this->get( '/wp-admin/admin.php?page=bible-plugin&tab=customization', [
			'page' => 'bible-plugin',
			'tab'  => 'customization'
		] );
		$this->assertEquals( 200, $response->getStatusCode() );
	}
}
