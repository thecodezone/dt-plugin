<?php

namespace Tests;

class PluginTest extends TestCase {
	public function test_plugin_installed() {
		activate_plugin( 'bible-reader/bible-reader.php' );

		$this->assertContains(
			'bible-reader/bible-reader.php',
			get_option( 'active_plugins' )
		);
	}
}
