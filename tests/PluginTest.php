<?php

namespace Tests;

/**
 * @test
 */
class PluginTest extends TestCase {
    /**
     * @test
     */
    public function can_install() {
        activate_plugin( 'dt-plugin/dt-plugin.php' );

        $this->assertContains(
            'dt-plugin/dt-plugin.php',
            get_option( 'active_plugins' )
        );
    }
}
