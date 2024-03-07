<?php

namespace Tests;

use function CodeZone\Bible\set_plugin_option;

require "vendor-scoped/autoload.php";

/**
 * Class BibleBrainsSettingsTest
 *
 * This class is responsible for testing the BibleBrains settings page.
 *
 * @test
 */
class TranslatorsTest extends TestCase {
	/**
	 * @test
	 */
	public function it_translates() {
		$this->assertEquals( 'Hello World', \CodeZone\Bible\translate( 'Hello World' ) );
		$this->assertEquals( 'Hello World', __( 'Hello World' ) );
	}

	/**
	 * @test
	 */
	public function it_custom_translates() {
		set_plugin_option( 'translations', [
			'Hello World' => 'Hola Mundo'
		] );
		$this->assertEquals( 'Hola Mundo', \CodeZone\Bible\translate( 'Hello World' ) );
		$this->assertEquals( 'Hola Mundo', __( 'Hello World' ) );
	}
}
