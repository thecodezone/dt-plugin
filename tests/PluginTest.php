<?php

namespace Tests;

class PluginTest extends TestCase {
	public function test_plugin_installed() {
		activate_plugin( 'bible-plugin/bible-plugin.php' );

		$this->assertContains(
			'bible-plugin/bible-plugin.php',
			get_option( 'active_plugins' )
		);
	}
}
